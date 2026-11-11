<div class="overflow-x-auto">
    @php($report = $getRecord())
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 dark:bg-white/5">
            <tr>
                <th class="px-3 py-2 font-semibold text-gray-700 dark:text-gray-200">No</th>
                <th class="px-3 py-2 font-semibold text-gray-700 dark:text-gray-200">Lembaga</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">TK/PAUD</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K1</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K2</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K3</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K4</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K5</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">K6</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">SMK</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Guru</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Balita</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Bumil</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Busui</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Kader</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Jumlah</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
            @foreach ($report->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $detail->institution?->nama ?? '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->tk_paud ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_1 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_2 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_3 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_4 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_5 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kelas_6 ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->smk ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->guru ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->balita ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->bumil ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->busui ?: '-' }}</td>
                    <td class="px-3 py-2 text-center">{{ $detail->kader ?: '-' }}</td>
                    <td class="px-3 py-2 text-center font-bold text-primary-600 dark:text-primary-400">{{ $detail->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-gray-200 dark:border-white/20">
                <td colspan="15" class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Total Penerima Manfaat</td>
                <td class="px-3 py-2 text-center text-base font-bold text-primary-600 dark:text-primary-400">{{ $report->total }}</td>
            </tr>
        </tfoot>
    </table>
</div>
