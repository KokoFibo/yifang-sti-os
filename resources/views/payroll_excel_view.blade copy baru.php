<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payroll Excel View</title>
</head>

<body>
    <table class="table">
        <thead>
            {{-- Header Judul Laporan --}}
            <tr>
                <th colspan="57" style="font-size: 20px; text-align: center;">
                    <h3>{{ $header_text }}</h3>
                </th>
            </tr>

            {{-- Info Filter/Search jika tersedia --}}
            @if (!empty($search))
                <tr>
                    <th colspan="2">
                        <h3>Search by:</h3>
                    </th>
                    <th colspan="55">
                        <h3>{{ $search }}</h3>
                    </th>
                </tr>
            @endif
            @if (!empty($nama_directorate))
                <tr>
                    <th colspan="2">
                        <h3>Directorate:</h3>
                    </th>
                    <th colspan="55">
                        <h3>{{ $nama_directorate }}</h3>
                    </th>
                </tr>
            @endif
            @if (!empty($nama_company))
                <tr>
                    <th colspan="2">
                        <h3>Company:</h3>
                    </th>
                    <th colspan="55">
                        <h3>{{ $nama_company }}</h3>
                    </th>
                </tr>
            @endif
            @if (!empty($nama_departement))
                <tr>
                    <th colspan="2">
                        <h3>Department:</h3>
                    </th>
                    <th colspan="55">
                        <h3>{{ $nama_departement }}</h3>
                    </th>
                </tr>
            @endif
            @if (!empty($nama_placement2))
                <tr>
                    <th colspan="2">
                        <h3>Placement:</h3>
                    </th>
                    <th colspan="55">
                        <h3>{{ $nama_placement2 }}</h3>
                    </th>
                </tr>
            @endif

            {{-- Sub-header Kelompok BPJS --}}
            <tr>
                <th colspan="37"></th>
                <th colspan="5" style="text-align: center; background-color: #28a745; color: #000000;">
                    <h4>Karyawan</h4>
                </th>
                <th colspan="5" style="text-align: center; background-color: #007bff; color: #ffffff;">
                    <h4>Company</h4>
                </th>
                <th colspan="10"></th>
            </tr>

            {{-- Header Kolom --}}
            <tr>
                <th style="text-align: center;">ID Karyawan</th>
                <th style="text-align: center;">Nama Karyawan</th>
                <th style="text-align: center;">Bank</th>
                <th style="text-align: center;">No. Rekening</th>
                <th style="text-align: center;">Jabatan</th>
                <th style="text-align: center;">Tanggal Bergabung</th>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Directorate</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Placement</th>
                <th style="text-align: center;">Metode Penggajian</th>
                <th style="text-align: center;">Total Hari Kerja</th>
                <th style="text-align: center;">Total Jam Kerja (Bersih)</th>
                <th style="text-align: center;">Total Jam Lembur</th>
                <th style="text-align: center;">Total Jam Kerja Libur</th>
                <th style="text-align: center;">Total Jam Lembur Libur</th>
                <th style="text-align: center;">Jumlah Jam Terlambat</th>
                <th style="text-align: center;">Tambahan Shift Malam</th>
                <th style="text-align: center;">Gaji Pokok</th>
                <th style="text-align: center;">Gaji Per Hari/Jam</th>
                <th style="text-align: center;">Gaji Bulan Ini</th>
                <th style="text-align: center;">Gaji Lembur</th>
                <th style="text-align: center;">Total Gaji Lembur</th>
                <th style="text-align: center;">Total Gaji Lembur Libur</th>
                <th style="text-align: center;">Gaji Libur</th>
                <th style="text-align: center;">Bonus/U.Makan</th>
                <th style="text-align: center;">THR</th>
                <th style="text-align: center;">Potongan 1X</th>
                <th style="text-align: center;">Total NoScan</th>
                <th style="text-align: center;">Denda Lupa Absen</th>
                <th style="text-align: center;">Denda Resigned</th>
                <th style="text-align: center;">Tanggungan</th>
                <th style="text-align: center;">Iuran Air</th>
                <th style="text-align: center;">Iuran Locker</th>
                <th style="text-align: center;">Status Karyawan</th>
                <th style="text-align: center;">Gaji BPJS with adjustment</th>
                <th style="text-align: center;">Gaji BPJS</th>
                <th style="text-align: center;">JHT</th>
                <th style="text-align: center;">JP</th>
                <th style="text-align: center;">JKK</th>
                <th style="text-align: center;">JKM</th>
                <th style="text-align: center;">Kesehatan</th>
                <th style="text-align: center;">JHT</th>
                <th style="text-align: center;">JP</th>
                <th style="text-align: center;">JKK</th>
                <th style="text-align: center;">JKM</th>
                <th style="text-align: center;">Kesehatan</th>
                {{-- <th style="text-align: center;">Total BPJS</th> --}}
                <th style="text-align: center;">Total TAX</th>
                <th style="text-align: center;">PTKP</th>
                <th style="text-align: center;">TER</th>
                <th style="text-align: center;">Rate</th>
                <th style="text-align: center;">Pph21</th>
                <th style="text-align: center;">NIK</th>
                <th style="text-align: center;">No NPWP</th>
                <th style="text-align: center;">Total</th>
                {{-- <th style="text-align: center; background-color: #63BDF6;">BPJS Employee</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                @php
                    // Capping Batas Maksimal Gaji BPJS
                    $gajiBpjsMax = min($d->gaji_bpjs, 12000000);
                    $gajiJpMax = min($d->gaji_bpjs, 11086300);

                    // BPJS Porsi Perusahaan
                    $kesehatanCompany = $d->kesehatan != 0 ? $gajiBpjsMax * 0.04 : 0;
                    $jkkRate = $d->company_id == 101 ? 0.0089 : 0.0024;
                    $jkkCompany = $d->jkk ? $d->gaji_bpjs * $jkkRate : 0;
                    $jkmCompany = $d->jkm ? $d->gaji_bpjs * 0.003 : 0;
                    $jpCompany = $d->jp != 0 ? $gajiJpMax * 0.02 : 0;
                    $jhtCompany = $d->gaji_bpjs * 0.037;

                    // Basis Perhitungan Pajak / Total TAX
                    $totalTax =
                        $d->bpjs_adjustment +
                        $jkkCompany +
                        $jkmCompany +
                        $kesehatanCompany +
                        $d->gaji_lembur * ($d->jam_lembur + $d->jam_lembur_libur) +
                        $d->gaji_libur +
                        $d->bonus1x +
                        $d->thr +
                        $d->tambahan_shift_malam;

                    // Mapping TER
                    $terMap = [
                        'TK0' => 'A',
                        'TK1' => 'A',
                        'K0' => 'A',
                        'TK2' => 'B',
                        'TK3' => 'B',
                        'K1' => 'B',
                        'K2' => 'B',
                        'K3' => 'C',
                    ];
                    $ter = $terMap[$d->ptkp] ?? '';
                    $ratePph21 = get_rate_ter_pph21($d->ptkp, $totalTax);

                    // Gaji per Hari / Jam Rate
                    $gajiRate =
                        $d->metode_penggajian === 'Perjam'
                            ? $d->gaji_pokok / 198
                            : $d->gaji_pokok / ($total_n_hari_kerja ?? 1);
                @endphp

                <tr>
                    <td style="text-align: center;">{{ $d->id_karyawan }}</td>
                    <td>{{ $d->nama }}</td>
                    <td style="text-align: center;">{{ $d->nama_bank }}</td>
                    <td style="text-align: center;">{{ strval($d->nomor_rekening) }}</td>
                    <td style="text-align: center;">{{ nama_jabatan($d->jabatan_id) }}</td>
                    <td style="text-align: center;">{{ tgl_bergabung($d->id_karyawan) }}</td>
                    <td style="text-align: center;">{{ nama_company($d->company_id) }}</td>
                    <td style="text-align: center;">{{ nama_placement($d->placement_id) }}</td>
                    <td style="text-align: center;">{{ nama_department($d->department_id) }}</td>
                    <td style="text-align: center;">{{ nama_placement2($d->placement2_id) }}</td>
                    <td style="text-align: center;">{{ $d->metode_penggajian }}</td>
                    <td>{{ $d->hari_kerja }}</td>
                    <td>{{ $d->jam_kerja }}</td>
                    <td>{{ $d->jam_lembur }}</td>
                    <td>{{ $d->jam_kerja_libur }}</td>
                    <td>{{ $d->jam_lembur_libur }}</td>
                    <td>{{ $d->jumlah_jam_terlambat }}</td>
                    <td style="text-align: right;">{{ $d->tambahan_shift_malam }}</td>
                    <td style="text-align: right;">{{ $d->gaji_pokok }}</td>
                    <td style="text-align: right;">{{ $gajiRate }}</td>
                    <td style="text-align: right;">{{ $d->gaji_bulan_ini }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur * $d->jam_lembur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur * $d->jam_lembur_libur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_libur }}</td>
                    <td style="text-align: right;">{{ $d->bonus1x }}</td>
                    <td style="text-align: right;">{{ $d->thr }}</td>
                    <td style="text-align: right;">{{ $d->potongan1x }}</td>
                    <td>{{ $d->total_noscan }}</td>
                    <td style="text-align: right;">{{ $d->denda_lupa_absen }}</td>
                    <td style="text-align: right;">{{ $d->denda_resigned }}</td>
                    <td>{{ $d->tanggungan }}</td>
                    <td style="text-align: right;">{{ $d->iuran_air }}</td>
                    <td style="text-align: right;">{{ $d->iuran_locker }}</td>
                    <td style="text-align: center;">{{ $d->status_karyawan }}</td>

                    {{-- BPJS Karyawan --}}
                    <td style="text-align: right;">{{ $d->bpjs_adjustment }}</td>
                    <td style="text-align: right;">{{ $d->gaji_bpjs }}</td>
                    <td style="text-align: right;">{{ $d->jht }}</td>
                    <td style="text-align: right;">{{ $d->jp }}</td>
                    <td style="text-align: right;">{{ $d->jkk == 1 ? 'Yes' : 'No' }}</td>
                    <td style="text-align: right;">{{ $d->jkm == 1 ? 'Yes' : 'No' }}</td>
                    <td style="text-align: right;">{{ $d->kesehatan }}</td>

                    {{-- BPJS Company --}}
                    <td style="text-align: right;">{{ $d->jht > 0 ? $jhtCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jp > 0 ? $jpCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jkk == 1 ? $jkkCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jkm == 1 ? $jkmCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->kesehatan > 0 ? $kesehatanCompany : '' }}</td>

                    {{-- Total BPJS & Tax --}}
                    {{-- <td style="text-align: right;">{{ $d->total_bpjs }}</td> --}}
                    <td style="text-align: right;">{{ $totalTax }}</td>
                    <td style="text-align: right;">{{ $d->ptkp }}</td>
                    <td style="text-align: right;">{{ $ter }}</td>
                    <td style="text-align: right;">{{ $ratePph21 }}</td>
                    <td style="text-align: right;">{{ $d->pph21 }}</td>

                    {{-- Identitas & Total --}}
                    <td style="text-align: right;">'{{ trim(strval(nik($d->id_karyawan))) }}</td>
                    <td style="text-align: right;">'{{ trim(strval(no_npwp($d->id_karyawan))) }}</td>
                    <td style="text-align: right;">{{ $d->total }}</td>

                    {{-- Summary Final --}}
                    {{-- <td style="text-align: right;">{{ $d->bpjs_employee }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
