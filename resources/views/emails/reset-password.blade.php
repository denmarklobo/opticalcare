<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f6f9fc; 
        }
        .container { 
            max-width: 600px; 
            margin: auto; 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        h1 { 
            color: #333; 
            font-size: 24px; 
            margin-bottom: 10px; 
        }
        p { 
            color: #555; 
            line-height: 1.6; 
        }
        .button { 
            display: inline-block; 
            background-color: #007bff; 
            color: #ffffff; 
            padding: 12px 25px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 20px 0; 
            transition: background-color 0.3s;
        }
        .button:hover { 
            background-color: #0056b3; 
        }
        .footer { 
            margin-top: 20px; 
            font-size: 12px; 
            color: #777; 
            text-align: center; 
        }
        .footer a { 
            color: #007bff; 
            text-decoration: none; 
        }
        .footer a:hover { 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Request</h1>
        <p>Hello {{ $name }},</p>
        <p>We received a request to reset your password. Click the button below to set a new password for your account.</p>
        <a href="{{ $url }}" class="button">Set New Password</a>
        <p>If you didn’t request a password reset, please ignore this email.</p>
        <p>For security reasons, please choose a strong password that you haven’t used before.</p>
        <div class="footer">
            <p>Thank you for using our application!<br>Best Regards,<br>Team ECHODE</p>
        </div>
    </div>
</body>
</html>
