<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode OTP Reset Password</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f6f9; color: #1f2937; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f4f6f9; padding: 32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 500px; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb;">
                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 32px 24px; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px;">SIMPAD</h1>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #bfdbfe; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Sistem Presensi & Alumni Digital</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 32px 24px;">
                            <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600; color: #111827; text-align: center;">Permintaan Reset Password</h2>
                            
                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #4b5563; text-align: center;">
                                Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password:
                            </p>
                            
                            <!-- OTP Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: #f3f4f6; border: 1px dashed #d1d5db; border-radius: 12px; padding: 16px 32px; text-align: center;">
                                            <span style="font-size: 32px; font-weight: 800; color: #111827; letter-spacing: 6px; margin-left: 6px;">{{ $otpCode }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 0 0 24px; font-size: 13px; line-height: 1.5; color: #e11d48; text-align: center; font-weight: 500;">
                                * Kode OTP ini hanya berlaku selama 10 menit demi keamanan akun Anda.
                            </p>
                            
                            <hr style="border: 0; border-top: 1px solid #f3f4f6; margin: 24px 0;">
                            
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #6b7280; text-align: center;">
                                Abaikan email ini jika Anda tidak merasa melakukan permintaan ini. Keamanan akun Anda adalah prioritas kami.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 24px; background: #f9fafb; border-top: 1px solid #f3f4f6; color: #9ca3af; font-size: 11px; text-align: center; line-height: 1.5;">
                            Email ini dikirim secara otomatis oleh Sistem Presensi & Alumni Digital Sekolah.<br>
                            &copy; {{ date('Y') }} SIMPAD. Hak Cipta Dilindungi.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
