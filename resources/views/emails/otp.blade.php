<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <p class="header">Hello {{ $user->name ?? 'User' }},</p>
        <p>You have successfully logged in to your account.</p>
        <p>If this was not you, please secure your account immediately by changing your password.</p>
        <p>Thanks,<br>Your App Team</p>
        <p class="footer">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>

</html>