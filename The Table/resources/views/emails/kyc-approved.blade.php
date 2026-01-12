<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .alert {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ KYC Approved!</h1>
        </div>
        <div class="content">
            <h2>Congratulations {{ $user->name }}!</h2>
            
            <div class="alert">
                Your KYC verification has been approved. You can now participate in all partnership opportunities!
            </div>
            
            <p>You now have full access to:</p>
            <ul>
                <li>Browse and join partnership cohorts</li>
                <li>Make contributions and participate in partnerships</li>
                <li>Receive distributions and returns</li>
                <li>Access your complete portfolio</li>
            </ul>
            
            <center>
                <a href="{{ config('app.url') }}/cohorts" class="button">Browse Partnership Cohorts</a>
            </center>
            
            <p>Thank you for completing your verification. We're excited to have you as part of our investment community!</p>
            
            <p>Best regards,<br><strong>The RoundTable Team</strong></p>
        </div>
    </div>
</body>
</html>
