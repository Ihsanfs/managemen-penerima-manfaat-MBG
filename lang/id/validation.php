<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             , ':attribute harus diterima.',
    'accepted_if'          , ':attribute harus diterima ketika :other bernilai :value.',
    'active_url'           , ':attribute bukan URL yang valid.',
    'after'                , ':attribute harus berupa tanggal setelah :date.',
    'after_or_equal'       , ':attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha'                , ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           , ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            , ':attribute hanya boleh berisi huruf dan angka.',
    'array'                , ':attribute harus berupa sebuah array.',
    'ascii'                , ':attribute hanya boleh berisi karakter alfanumerik dan simbol byte tunggal.',
    'before'               , ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal'      , ':attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between'              , [
        'array'   , ':attribute harus memiliki antara :min dan :max item.',
        'file'    , ':attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' , ':attribute harus bernilai antara :min dan :max.',
        'string'  , ':attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean'              , ':attribute harus bernilai true atau false.',
    'can'                  , ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed'            , 'Konfirmasi :attribute tidak cocok.',
    'current_password'     , 'Kata sandi salah.',
    'date'                 , ':attribute bukan tanggal yang valid.',
    'date_equals'          , ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          , ':attribute tidak cocok dengan format :format.',
    'decimal'              , ':attribute harus memiliki :decimal tempat desimal.',
    'declined'             , ':attribute harus ditolak.',
    'declined_if'          , ':attribute harus ditolak ketika :other bernilai :value.',
    'different'            , ':attribute dan :other harus bernilai berbeda.',
    'digits'               , ':attribute harus berisi :digits digit.',
    'digits_between'       , ':attribute harus berisi antara :min dan :max digit.',
    'dimensions'           , ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             , ':attribute memiliki nilai yang duplikat.',
    'doesnt_end_with'      , ':attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with'    , ':attribute tidak boleh diawali dengan salah satu dari berikut: :values.',
    'email'                , ':attribute harus berupa alamat surel yang valid.',
    'ends_with'            , ':attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum'                 , ':attribute yang dipilih tidak valid.',
    'exists'               , ':attribute yang dipilih tidak valid.',
    'extensions'           , ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file'                 , ':attribute harus berupa sebuah berkas.',
    'filled'               , ':attribute wajib diisi.',
    'gt'                   , [
        'array'   , ':attribute harus memiliki lebih dari :value item.',
        'file'    , ':attribute harus berukuran lebih besar dari :value kilobita.',
        'numeric' , ':attribute harus bernilai lebih besar dari :value.',
        'string'  , ':attribute harus berisi lebih besar dari :value karakter.',
    ],
    'gte'                  , [
        'array'   , ':attribute harus memiliki :value item atau lebih.',
        'file'    , ':attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'numeric' , ':attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'string'  , ':attribute harus berisi lebih besar dari atau sama dengan :value karakter.',
    ],
    'image'                , ':attribute harus berupa gambar.',
    'in'                   , ':attribute yang dipilih tidak valid.',
    'in_array'             , ':attribute tidak ada di dalam :other.',
    'integer'              , ':attribute harus berupa bilangan bulat.',
    'ip'                   , ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 , ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 , ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 , ':attribute harus berupa string JSON yang valid.',
    'lowercase'            , ':attribute harus berupa huruf kecil.',
    'lt'                   , [
        'array'   , ':attribute harus memiliki kurang dari :value item.',
        'file'    , ':attribute harus berukuran kurang dari :value kilobita.',
        'numeric' , ':attribute harus bernilai kurang dari :value.',
        'string'  , ':attribute harus berisi kurang dari :value karakter.',
    ],
    'lte'                  , [
        'array'   , ':attribute harus memiliki tidak lebih dari :value item.',
        'file'    , ':attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' , ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'string'  , ':attribute harus berisi kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'          , ':attribute harus berupa alamat MAC yang valid.',
    'max'                  , [
        'array'   , ':attribute tidak boleh memiliki lebih dari :max item.',
        'file'    , ':attribute tidak boleh berukuran lebih besar dari :max kilobita.',
        'numeric' , ':attribute tidak boleh bernilai lebih besar dari :max.',
        'string'  , ':attribute tidak boleh berisi lebih besar dari :max karakter.',
    ],
    'max_digits'           , ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes'                , ':attribute harus berupa berkas berjenis: :values.',
    'mimetypes'            , ':attribute harus berupa berkas berjenis: :values.',
    'min'                  , [
        'array'   , ':attribute harus memiliki minimal :min item.',
        'file'    , ':attribute harus berukuran minimal :min kilobita.',
        'numeric' , ':attribute harus bernilai minimal :min.',
        'string'  , ':attribute harus berisi minimal :min karakter.',
    ],
    'min_digits'           , ':attribute harus memiliki minimal :min digit.',
    'missing'              , ':attribute harus kosong.',
    'missing_if'           , ':attribute harus kosong ketika :other bernilai :value.',
    'missing_unless'       , ':attribute harus kosong kecuali :other bernilai :value.',
    'missing_with'         , ':attribute harus kosong ketika :values ada.',
    'missing_with_all'     , ':attribute harus kosong ketika :values ada.',
    'multiple_of'          , ':attribute harus merupakan kelipatan dari :value.',
    'not_in'               , ':attribute yang dipilih tidak valid.',
    'not_regex'            , 'Format :attribute tidak valid.',
    'numeric'              , ':attribute harus berupa angka.',
    'password'             , [
        'letters'       , ':attribute harus berisi setidaknya satu huruf.',
        'mixed'         , ':attribute harus berisi setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers'       , ':attribute harus berisi setidaknya satu angka.',
        'symbols'       , ':attribute harus berisi setidaknya satu simbol.',
        'uncompromised' , ':attribute yang diberikan pernah bocor dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present'              , ':attribute wajib ada.',
    'prohibited'           , ':attribute dilarang diisi.',
    'prohibited_if'        , ':attribute dilarang diisi ketika :other bernilai :value.',
    'prohibited_unless'    , ':attribute dilarang diisi kecuali :other ada di dalam :values.',
    'prohibits'            , ':attribute melarang :other untuk dikosongkan.',
    'regex'                , 'Format :attribute tidak valid.',
    'required'             , ':attribute tidak boleh kosong.',
    'required_array_keys'  , ':attribute harus berisi entri untuk: :values.',
    'required_if'          , ':attribute tidak boleh kosong ketika :other adalah :value.',
    'required_if_accepted' , ':attribute tidak boleh kosong ketika :other diterima.',
    'required_unless'      , ':attribute tidak boleh kosong kecuali :other ada di dalam :values.',
    'required_with'        , ':attribute tidak boleh kosong ketika :values ada.',
    'required_with_all'    , ':attribute tidak boleh kosong ketika :values ada.',
    'required_without'     , ':attribute tidak boleh kosong ketika :values tidak ada.',
    'required_without_all' , ':attribute tidak boleh kosong ketika tidak ada :values yang ada.',
    'same'                 , ':attribute dan :other harus sama.',
    'size'                 , [
        'array'   , ':attribute harus berisi :size item.',
        'file'    , ':attribute harus berukuran :size kilobita.',
        'numeric' , ':attribute harus berukuran :size.',
        'string'  , ':attribute harus berisi :size karakter.',
    ],
    'starts_with'          , ':attribute harus diawali dengan salah satu dari berikut: :values.',
    'string'               , ':attribute harus berupa teks.',
    'timezone'             , ':attribute harus berupa zona waktu yang valid.',
    'unique'               , ':attribute sudah terdaftar.',
    'uploaded'             , ':attribute gagal diunggah.',
    'uppercase'            , ':attribute harus berupa huruf besar.',
    'url'                  , ':attribute harus berupa URL yang valid.',
    'ulid'                 , ':attribute harus berupa ULID yang valid.',
    'uuid'                 , ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quickly
    | convenient to specify a specific custom language line.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | the following language files are used to translate our attribute placeholders
    | to something clean such as "E-Mail Address" instead of "email". This
    | makes it completely_id nice and natural.
    |
    */

    'attributes' => [
        'nama' => 'Nama',
        'nama_lembaga' => 'Nama Lembaga',
        'nik' => 'NIK',
        'nisn' => 'NISN',
        'jenis_kelamin' => 'Jenis Kelamin',
        'kategori' => 'Kategori',
        'kelas' => 'Kelas',
        'jurusan' => 'Jurusan',
        'status' => 'Status',
        'bulan' => 'Bulan Data',
        'tahun' => 'Tahun Data',
        'keterangan' => 'Keterangan',
        'institution_id' => 'Lembaga',
        'tipe' => 'Tipe Lembaga',
        'jenjang' => 'Jenjang Sekolah',
        'alamat' => 'Alamat',
        'penanggung_jawab' => 'Penanggung Jawab',
        'telepon' => 'Telepon',
        'urutan' => 'Urutan Tampil',
        'alasan' => 'Alasan',
        'alasan_perubahan' => 'Alasan Perubahan',
    ],

];
