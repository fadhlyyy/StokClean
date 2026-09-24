<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi StokClean</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f3f4fa; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- MAIN CARD -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 520px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1b2942 0%, #111a2c 100%); padding: 36px 30px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td style="background-color: #ffffff; width: 50px; height: 50px; border-radius: 12px; text-align: center; vertical-align: middle; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <span style="font-size: 24px; font-weight: bold; color: #1b2942; line-height: 50px;">⚡</span>
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 16px 0 4px 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">StokClean</h1>
                            <p style="margin: 0; color: #94a3b8; font-size: 13px; font-weight: 500;">Manajemen Stok Kebersihan &amp; Sanitasi</p>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 36px 32px 28px 32px;">
                            <p style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #0f172a;">Halo, {{ $userName }} 👋</p>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Kami menerima permintaan masuk ke akun Anda. Gunakan kode verifikasi (OTP) 6 digit di bawah ini untuk menyelesaikan proses login:
                            </p>

                            <!-- OTP CODE BOX -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 24px 0;">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 20px 36px; text-align: center;">
                                            <span style="display: block; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #64748b; margin-bottom: 8px;">Kode Verifikasi Anda</span>
                                            <span style="font-family: 'Courier New', Courier, monospace, monospace; font-size: 36px; font-weight: 800; letter-spacing: 10px; color: #1b2942; display: block;">
                                                {{ $otp }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- EXPIRATION ALERT -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 13px; color: #92400e; line-height: 1.5;">
                                        ⏱️ <strong>Batas Waktu:</strong> Kode ini hanya berlaku selama <strong>5 menit</strong> sejak email ini dikirimkan.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b; line-height: 1.5;">
                                🔒 <strong>Demi keamanan akun Anda:</strong> Jangan pernah membagikan kode verifikasi ini kepada siapa pun, termasuk staf atau pemilik gudang StokClean.
                            </p>

                            <p style="margin: 20px 0 0 0; font-size: 13px; color: #94a3b8; line-height: 1.5; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                                Jika Anda tidak merasa mencoba masuk, kemungkinan ada pihak lain yang mencoba mengakses akun Anda. Segera amankan kata sandi Anda.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} StokClean. Hak cipta dilindungi.
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #cbd5e1;">
                                Email otomatis ini dikirim oleh sistem keamanan StokClean.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
