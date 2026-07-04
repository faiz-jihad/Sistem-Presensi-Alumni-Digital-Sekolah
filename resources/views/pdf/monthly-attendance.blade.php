<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #10B981;
            padding-bottom: 8px;
        }
        .header h2 {
            margin: 0;
            color: #065F46;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 15%;
            font-weight: bold;
        }
        .meta-table td.value {
            width: 35%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .report-table th {
            background-color: #10B981;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            border: 1px solid #ddd;
        }
        .report-table td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .percentage-badge {
            font-weight: bold;
            color: #047857;
        }
        .percentage-low {
            color: #b91c1c;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Rekapitulasi Presensi Bulanan</h2>
        <p>Sistem Informasi Manajemen Presensi & Alumni Digital (SIMPAD)</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Kelas:</td>
            <td class="value">{{ $class['name'] }} ({{ $class['major'] }})</td>
            <td class="label">Periode:</td>
            <td class="value">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Siswa:</td>
            <td class="value">{{ $total_students }} Siswa</td>
            <td class="label">Dicetak Pada:</td>
            <td class="value">{{ now()->locale('id')->isoFormat('D MMMM Y H:i') }} WIB</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Siswa</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 7%;">Hadir</th>
                <th style="width: 7%;">Telat</th>
                <th style="width: 7%;">Sakit</th>
                <th style="width: 7%;">Izin</th>
                <th style="width: 7%;">Alpha</th>
                <th style="width: 10%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($students as $student)
                @php
                    $isLow = $student['attendance_percentage'] < 80;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>{{ $student['name'] }}</strong></td>
                    <td class="text-center">{{ $student['nis'] }}</td>
                    <td class="text-center">{{ $student['summary']['present'] }}</td>
                    <td class="text-center">{{ $student['summary']['late'] }}</td>
                    <td class="text-center">{{ $student['summary']['sick'] }}</td>
                    <td class="text-center">{{ $student['summary']['permission'] }}</td>
                    <td class="text-center">{{ $student['summary']['absent'] }}</td>
                    <td class="text-center percentage-badge {{ $isLow ? 'percentage-low' : '' }}">
                        {{ $student['attendance_percentage'] }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%; font-size: 9px; vertical-align: top;">
                <p><strong>Keterangan Kriteria Kehadiran:</strong></p>
                <p>- Persentase kehadiran dihitung dari: (Hadir + Terlambat) / Total Hari Rekam Presensi.</p>
                <p>- Persentase berwarna <span style="color: #b91c1c; font-weight: bold;">Merah</span> menunjukkan tingkat kehadiran di bawah 80%.</p>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: bottom;">
                <div style="font-size: 10px;">
                    <p>Kepala Sekolah / Wali Kelas</p>
                    <br><br><br>
                    <p>__________________________</p>
                    <p style="font-weight: bold; margin-right: 45px;">NIP. .........................</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
