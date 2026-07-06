<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Alumni</title>
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
            border-bottom: 2px solid #10B981;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #065F46;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #666;
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
            background-color: #10B981;
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
            background-color: #f0fdf4;
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
        .badge-verified { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }

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

    <div class="header">
        <h2>Laporan Data Alumni</h2>
        <p>{{ $school_name }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Tahun Lulus:</td>
            <td class="value">{{ $graduation_year ?? 'Semua Tahun' }}</td>
            <td class="label">Tanggal Cetak:</td>
            <td class="value">{{ now()->locale('id')->isoFormat('D MMMM Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="label">Status Verifikasi:</td>
            <td class="value">
                @if(($verification_status ?? '') === 'verified')
                    Terverifikasi
                @elseif(($verification_status ?? '') === 'pending')
                    Menunggu Verifikasi
                @elseif(($verification_status ?? '') === 'rejected')
                    Ditolak
                @else
                    Semua Status
                @endif
            </td>
            <td class="label">Total Alumni:</td>
            <td class="value">{{ count($alumni_list) }} orang</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Alumni</th>
                <th style="width: 15%;">NISN</th>
                <th style="width: 10%;">L/P</th>
                <th style="width: 15%;">Lulusan</th>
                <th style="width: 20%;">Kontak</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni_list as $index => $alumni)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $alumni['name'] }}</strong>
                    </td>
                    <td>{{ $alumni['nisn'] ?: '-' }}</td>
                    <td>{{ ($alumni['gender'] ?? '') === 'male' ? 'L' : 'P' }}</td>
                    <td>
                        Tahun {{ $alumni['graduation_year'] }}<br>
                        <small>{{ $alumni['class_name'] }} ({{ $alumni['major'] ?: '-' }})</small>
                    </td>
                    <td>
                        {{ $alumni['email'] ?: '-' }}<br>
                        <small>{{ $alumni['phone'] ?: '-' }}</small>
                    </td>
                    <td>
                        <span class="badge badge-{{ $alumni['verification_status'] }}">
                            @if($alumni['verification_status'] === 'verified')
                                Terverifikasi
                            @elseif($alumni['verification_status'] === 'pending')
                                Menunggu
                            @elseif($alumni['verification_status'] === 'rejected')
                                Ditolak
                            @else
                                {{ $alumni['verification_status'] }}
                            @endif
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data alumni.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem Presensi Alumni Digital Sekolah (SIMPAD) pada {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
