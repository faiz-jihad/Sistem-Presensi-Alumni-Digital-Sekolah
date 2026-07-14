<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lowongan Kerja Anda Disetujui</title>
</head>
<body style="margin: 0; padding: 0; background: #f6f8fb; color: #1f2937; font-family: Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f6f8fb; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="background: #2563eb; padding: 22px 28px;">
                            <h1 style="margin: 0; font-size: 22px; line-height: 1.3; color: #ffffff;">Lowongan Kerja Disetujui</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px;">
                            <p style="margin: 0 0 14px; font-size: 15px; line-height: 1.6;">Halo,</p>
                            <p style="margin: 0 0 14px; font-size: 15px; line-height: 1.6;">
                                Selamat! Pengajuan lowongan kerja Anda telah <strong>disetujui</strong> oleh admin dan sekarang sudah aktif tampil di platform SIMPAD.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #eff6ff; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 6px; font-size: 13px; color: #6b7280;">Posisi / Jabatan</p>
                                        <p style="margin: 0 0 12px; font-size: 16px; font-weight: bold; color: #1e40af;">{{ $job->title }}</p>
                                        <p style="margin: 0 0 6px; font-size: 13px; color: #6b7280;">Perusahaan</p>
                                        <p style="margin: 0 0 12px; font-size: 14px; color: #1f2937;">{{ $job->company_name }}</p>
                                        @if($job->location)
                                        <p style="margin: 0 0 6px; font-size: 13px; color: #6b7280;">Lokasi</p>
                                        <p style="margin: 0; font-size: 14px; color: #1f2937;">{{ $job->location }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #6b7280;">
                                Lowongan kerja ini kini dapat dilihat oleh seluruh alumni dan pencari kerja di platform. Terima kasih telah berkontribusi dalam membuka peluang karir bagi alumni kami.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 28px; background: #f9fafb; color: #6b7280; font-size: 12px;">
                            Email ini dikirim otomatis. Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
