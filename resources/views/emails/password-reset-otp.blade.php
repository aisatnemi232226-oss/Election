<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password reset code</title>
</head>
<body style="margin:0;background:#f3f4f6;color:#1f2937;font-family:Arial,sans-serif;">
    <main style="max-width:520px;margin:32px auto;padding:32px;background:#fff;border-top:4px solid #2563eb;">
        <h1 style="margin:0 0 16px;font-size:22px;">Password reset code</h1>
        <p style="line-height:1.6;">Use this one-time code to reset your Election System password:</p>
        <p style="margin:24px 0;padding:16px;background:#eff6ff;color:#1d4ed8;text-align:center;font-size:32px;font-weight:700;letter-spacing:8px;">
            {{ $otp }}
        </p>
        <p style="line-height:1.6;">The code expires in 10 minutes. If you did not request a password reset, you can ignore this email.</p>
    </main>
</body>
</html>