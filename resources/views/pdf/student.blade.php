<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Siswa</title>
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
        .kop-title {
            text-align: center;
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
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-inactive { background-color: #fee2e2; color: #991b1b; }
        .badge-graduated { background-color: #e0f2fe; color: #0369a1; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td class="kop-title">
                <h2>{{ strtoupper($school_name) }}</h2>
                <h3>SISTEM INFORMASI MANAJEMEN PRESENSI & ALUMNI DIGITAL</h3>
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
        <h2>LAPORAN DATA SISWA</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Kelas:</td>
            <td class="value">{{ $class_name ?? 'Semua Kelas' }}</td>
            <td class="label">Tanggal Cetak:</td>
            <td class="value">{{ now()->locale('id')->isoFormat('D MMMM Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value">{{ $status_label ?? 'Semua Status' }}</td>
            <td class="label">Total Siswa:</td>
            <td class="value">{{ count($student_list ?? []) }} orang</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 12%;">NISN</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 8%;">L/P</th>
                <th style="width: 13%;">Kelas</th>
                <th style="width: 15%;">No WA Orang Tua</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student_list as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['nis'] ?: '-' }}</td>
                    <td>{{ $student['nisn'] ?: '-' }}</td>
                    <td><strong>{{ $student['name'] }}</strong></td>
                    <td>{{ ($student['gender'] ?? '') === 'male' ? 'L' : 'P' }}</td>
                    <td>{{ $student['class_name'] ?: '-' }}</td>
                    <td>{{ $student['parent_phone'] ?: '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $student['status'] }}">
                            @if($student['status'] === 'active')
                                Aktif
                            @elseif($student['status'] === 'inactive')
                                Tidak Aktif
                            @elseif($student['status'] === 'graduated')
                                Lulus
                            @else
                                {{ $student['status'] }}
                            @endif
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->locale('id')->isoFormat('D MMMM Y HH:mm') }} WIB
        <br>
        Sistem Informasi Manajemen Presensi & Alumni Digital (SIMPAD)
    </div>

</body>
</html>
