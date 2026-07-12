<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Bulanan</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #333;
        line-height: 1.4;
    }

    .kop-table{
    width:100%;
    border-bottom:3px solid #000;
    margin-bottom:20px;
    border-collapse:collapse;
    }

    .kop-title{
        text-align:center;
    }

    .kop-title h2{
        margin:2px 0;
        font-size:22px;
        font-weight:bold;
    }

    .kop-title h3{
        margin:3px 0;
        font-size:16px;
    }

    .kop-title p{
        margin:2px 0;
        font-size:11px;
    }

    .header{
        text-align:center;
        margin-bottom:20px;
    }

    .header h2{
        margin:0;
        font-size:18px;
    }

    .meta-table{
        width:100%;
        margin:20px 0 15px;
        border-collapse:collapse;
    }

    .meta-table td{
        padding:4px 2px;
        vertical-align:top;
        font-size:11px;
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

    .summary-box{
        margin-top:20px;
        border:1px solid #ddd;
        padding:10px;
        border-radius:6px;
        background:#f8fafc;
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
        <h2>Laporan Rekapitulasi Presensi Bulanan</h2>
    </div>

    <table class="meta-table" style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:15%; font-weight:bold;">Kelas</td>
            <td style="width:35%;">: {{ $class['name'] }} ({{ $class['major'] }})</td>

            <td style="width:15%; font-weight:bold;">Periode</td>
            <td style="width:35%;">: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y') }}</td>
        </tr>

        <tr>
            <td style="font-weight:bold;">Total Siswa</td>
            <td>: {{ $total_students }} Siswa</td>

            <td style="font-weight:bold;">Tanggal</td>
            <td>: {{ now()->locale('id')->isoFormat('D MMMM Y HH:mm') }} WIB</td>
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

    <table style="width:100%; margin-top:20px;">
    <tr>

    <td style="width:50%; vertical-align:top;">

    <div class="summary-box">

    <h4>Informasi</h4>

    <ul class="summary-list">
    <li>Persentase dihitung dari (Hadir + Terlambat) / Total Hari Presensi.</li>
    <li>Persentase di bawah 80% dianggap kurang.</li>
    </ul>

    </div>

    </td>

    <td style="width:50%; vertical-align:bottom; text-align:right;">

    <div style="font-size:11px">

    <p>
    Dicetak pada:
    {{ now()->locale('id')->isoFormat('D MMMM Y H:i') }} WIB
    </p>

    <br><br><br>

    <p>__________________________</p>

    <p style="font-weight:bold; margin-right:40px;">
    Wali Kelas / Petugas
    </p>

    </div>

    </td>

    </tr>
    </table>

</body>
</html>
