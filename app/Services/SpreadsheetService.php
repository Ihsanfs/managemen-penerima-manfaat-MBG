<?php

namespace App\Services;

use DateTimeInterface;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetService
{
    /**
     * Buat unduhan file Excel dari beberapa sheet.
     *
     * Setiap sheet mendukung kunci:
     * title, headings, rows, text_columns (nomor kolom 1-based),
     * widths (nomor kolom => lebar), wrap, freeze, autofilter, header_fill.
     *
     * @param  array<int, array<string, mixed>>  $sheets
     */
    public function download(array $sheets, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach (array_values($sheets) as $index => $sheetData) {
            $sheet = new Worksheet($spreadsheet, $this->safeTitle($sheetData['title'] ?? 'Sheet' . ($index + 1)));
            $spreadsheet->addSheet($sheet);
            $this->fillSheet($sheet, $sheetData);
        }

        if ($spreadsheet->getSheetCount() === 0) {
            $spreadsheet->createSheet();
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer, $spreadsheet): void {
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Baca baris pertama sheet sebagai header dan kembalikan seluruh baris data.
     *
     * @return array{headers: array<int, string>, rows: array<int, array<string, mixed>>}
     */
    public function read(string $path, ?string $extension = null): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException('File yang diunggah tidak ditemukan.');
        }

        $reader = $this->makeReader($path, $extension);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($path);

        try {
            $sheet = $spreadsheet->getSheet(0);
            $data = $sheet->toArray(null, true, false, false);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }

        $headerIndex = null;

        foreach ($data as $index => $row) {
            if (collect($row)->contains(fn ($value) => $value !== null && trim((string) $value) !== '')) {
                $headerIndex = $index;

                break;
            }
        }

        if ($headerIndex === null) {
            throw new InvalidArgumentException('File tidak memiliki baris header.');
        }

        $headers = [];

        foreach ($data[$headerIndex] as $column => $header) {
            $headers[$column] = $this->normalizeHeader($header);
        }

        $rows = [];

        for ($index = $headerIndex + 1; $index < count($data); $index++) {
            $row = [];
            $hasValue = false;

            foreach ($headers as $column => $key) {
                $value = $data[$index][$column] ?? null;

                if (is_string($value)) {
                    $value = trim(str_replace("\u{00A0}", ' ', $value));
                }

                if ($value !== null && $value !== '') {
                    $hasValue = true;
                }

                if ($key !== '') {
                    $row[$key] = $value;
                }
            }

            if (! $hasValue) {
                continue;
            }

            $row['__baris'] = $index + 1;
            $rows[] = $row;
        }

        return [
            'headers' => array_values(array_filter($headers, fn (string $key): bool => $key !== '')),
            'rows' => $rows,
        ];
    }

    private function makeReader(string $path, ?string $extension): \PhpOffice\PhpSpreadsheet\Reader\IReader
    {
        $extension = strtolower((string) $extension);

        if ($extension === '') {
            $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        }

        if ($extension === '') {
            $reader = IOFactory::createReaderForFile($path);
        } else {
            $reader = match ($extension) {
                'xlsx' => IOFactory::createReader('Xlsx'),
                'xls' => IOFactory::createReader('Xls'),
                'csv', 'txt' => IOFactory::createReader('Csv'),
                default => throw new InvalidArgumentException('Format file harus .xlsx, .xls, atau .csv.'),
            };
        }

        if ($reader instanceof Csv) {
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter($this->detectDelimiter($path));
            $reader->setSheetIndex(0);
        }

        return $reader;
    }

    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return ',';
        }

        $line = (string) fgets($handle);
        fclose($handle);

        $candidates = [',' => 0, ';' => 0, "\t" => 0];

        foreach (array_keys($candidates) as $delimiter) {
            $candidates[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($candidates);
        $delimiter = array_key_first($candidates);

        return ($candidates[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    private function normalizeHeader(mixed $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);
        $header = strtolower(trim($header));
        $header = str_replace(['/', '-', '\\', ' '], '_', $header);
        $header = preg_replace('/_+/', '_', $header);

        return trim((string) $header, '_');
    }

    private function safeTitle(string $title): string
    {
        $title = str_replace(['\\', '/', '*', '?', ':', '[', ']'], ' ', $title);
        $title = trim((string) $title) ?: 'Sheet';
        $title = mb_substr($title, 0, 31);

        return $title;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillSheet(Worksheet $sheet, array $data): void
    {
        $headings = array_values($data['headings'] ?? []);
        $rows = array_values($data['rows'] ?? []);
        $textColumns = $data['text_columns'] ?? [];
        $widths = $data['widths'] ?? [];
        $wrap = (bool) ($data['wrap'] ?? false);
        $freeze = (bool) ($data['freeze'] ?? true);
        $useAutofilter = (bool) ($data['autofilter'] ?? true);
        $headerFill = (string) ($data['header_fill'] ?? 'FF059669');

        $columnCount = max(array_merge(
            [count($headings)],
            array_map(fn (array $row): int => count($row), $rows ?: [[]]),
            [1],
        ));

        $rowNumber = 0;

        if ($headings !== []) {
            $rowNumber = 1;
            $lastColumn = Coordinate::stringFromColumnIndex(count($headings));

            foreach ($headings as $index => $heading) {
                $sheet->setCellValueExplicit(
                    Coordinate::stringFromColumnIndex($index + 1) . '1',
                    (string) $heading,
                    DataType::TYPE_STRING,
                );
            }

            $headerRange = 'A1:' . $lastColumn . '1';
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
            $sheet->getStyle($headerRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(1)->setRowHeight(22);

            if ($freeze) {
                $sheet->freezePane('A2');
            }
        }

        foreach ($rows as $row) {
            $rowNumber++;

            foreach (array_values($row) as $index => $value) {
                $coordinate = Coordinate::stringFromColumnIndex($index + 1) . $rowNumber;

                if (in_array($index + 1, $textColumns, true)) {
                    $sheet->setCellValueExplicit(
                        $coordinate,
                        $value === null ? '' : (string) $value,
                        DataType::TYPE_STRING,
                    );
                } elseif ($value instanceof DateTimeInterface) {
                    $sheet->setCellValue($coordinate, Date::PHPToExcel($value));
                    $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                } else {
                    $sheet->setCellValue($coordinate, $value);
                }
            }
        }

        $lastRow = max($rowNumber, 1);
        $lastColumn = Coordinate::stringFromColumnIndex(max($columnCount, count($headings), 1));

        if ($headings !== [] && $lastRow > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText($wrap);
        } elseif ($headings === []) {
            $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText($wrap);
        }

        if ($headings !== []) {
            $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFE5E7EB');

            if ($useAutofilter) {
                $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);
            }
        }

        for ($column = 1; $column <= max($columnCount, count($headings)); $column++) {
            if (isset($widths[$column])) {
                $sheet->getColumnDimensionByColumn($column)->setWidth((float) $widths[$column]);

                continue;
            }

            $maxLength = 10;

            for ($row = 1; $row <= $lastRow; $row++) {
                $value = $sheet->getCell(Coordinate::stringFromColumnIndex($column) . $row)->getValue();

                if (is_scalar($value)) {
                    $maxLength = max($maxLength, min(45, mb_strlen((string) $value)));
                }
            }

            $sheet->getColumnDimensionByColumn($column)->setWidth(min(45, $maxLength + 2));
        }
    }
}
