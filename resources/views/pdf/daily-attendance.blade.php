<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Harian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #000;
            font-size: 18px;
            text-transform: uppercase;
        }
        .kop-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 90px;
            text-align: center;
        }

        .kop-logo img {
            width: 75px;
            height: 75px;
        }

        .kop-title {
            text-align: center;
        }

        .kop-title h4 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
        }

        .kop-title h2 {
            margin: 2px 0;
            font-size: 22px;
            font-weight: bold;
        }

        .kop-title h3 {
            margin: 3px 0;
            font-size: 16px;
        }

        .kop-title p {
            margin: 2px 0;
            font-size: 11px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
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
            margin-top: 10px;
        }
        .report-table th {
            background-color: #1E88E5;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .report-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .badge-present { background-color: #d1fae5; color: #065f46; }
        .badge-late { background-color: #fef3c7; color: #92400e; }
        .badge-permission { background-color: #e0f2fe; color: #075985; }
        .badge-sick { background-color: #f3e8ff; color: #6b21a8; }
        .badge-absent { background-color: #fee2e2; color: #991b1b; }
        .badge-not_recorded { background-color: #f1f5f9; color: #475569; }

        .summary-box {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            background-color: #f8fafc;
        }
        .summary-box h4 {
            margin: 0 0 8px 0;
            color: #1E88E5;
        }
        .summary-list {
            margin: 0;
            padding: 0 0 0 15px;
        }
        .summary-list li {
            margin-bottom: 4px;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
        <table class="kop-table">
        <tr>
            <td class="kop-title">

                <h2>{{ strtoupper($school_name) }}</h2>

                <h3>
                    SISTEM INFORMASI MANAJEMEN PRESENSI & ALUMNI DIGITAL
                </h3>

                <p>
                    {{ $school_address }}<br>

                    Telp. {{ $school_phone }}

                    @if($school_email)
                        - Email: {{ $school_email }}
                    @endif
                </p>

            </td>
        </tr>
    </table>

    <div class="header">
        <h2>Laporan Presensi Harian Siswa</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Kelas:</td>
            <td class="value">{{ $class['name'] }} ({{ $class['major'] }})</td>
            <td class="label">Tanggal:</td>
            <td class="value">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Siswa</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">Masuk</th>
                <th style="width: 10%;">Pulang</th>
                <th style="width: 10%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($students as $student)
                @php
                    $statusClass = 'badge-not_recorded';
                    $statusLabel = 'Belum Diisi';
                    
                    switch($student['status']) {
                        case 'present':
                            $statusClass = 'badge-present';
                            $statusLabel = 'Hadir';
                            break;
                        case 'late':
                            $statusClass = 'badge-late';
                            $statusLabel = 'Terlambat';
                            break;
                        case 'permission':
                            $statusClass = 'badge-permission';
                            $statusLabel = 'Izin';
                            break;
                        case 'sick':
                            $statusClass = 'badge-sick';
                            $statusLabel = 'Sakit';
                            break;
                        case 'absent':
                            $statusClass = 'badge-absent';
                            $statusLabel = 'Alpha';
                            break;
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td><strong>{{ $student['name'] }}</strong></td>
                    <td>{{ $student['nis'] }}</td>
                    <td>
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td style="text-align: center;">{{ $student['check_in_time'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $student['check_out_time'] ?? '-' }}</td>
                    <td>{{ $student['note'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="summary-box">
                    <h4>Ringkasan Kehadiran</h4>
                    <ul class="summary-list">
                        <li>Hadir Tepat Waktu: <strong>{{ $summary['present'] }}</strong> siswa</li>
                        <li>Terlambat: <strong>{{ $summary['late'] }}</strong> siswa</li>
                        <li>Sakit: <strong>{{ $summary['sick'] }}</strong> siswa</li>
                        <li>Izin: <strong>{{ $summary['permission'] }}</strong> siswa</li>
                        <li>Alpha / Absen: <strong>{{ $summary['absent'] }}</strong> siswa</li>
                        <li>Belum Terekam: <strong>{{ $summary['not_recorded'] }}</strong> siswa</li>
                    </ul>
                </div>
            </td>
            <td style="width: 50%; vertical-align: bottom; text-align: right;">
                <div style="font-size: 11px;">
                    <p>Dicetak pada: {{ now()->locale('id')->isoFormat('D MMMM Y H:i') }} WIB</p>
                    <br><br><br>
                    <p>__________________________</p>
                    <p style="font-weight: bold; margin-right: 40px;">Wali Kelas / Petugas</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
