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
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to RoundTable!</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>Welcome to the RoundTable Partnership Platform - where cooperative partnerships create lasting wealth!</p>
            
            <p>Your account has been successfully created. Here's what you can do next:</p>
            
            <ul>
                <li><strong>Complete KYC Verification:</strong> Verify your identity to unlock all features</li>
                <li><strong>Browse Cohorts:</strong> Explore available partnership opportunities</li>
                <li><strong>Join a Cohort:</strong> Start your cooperative partnership journey</li>
            </ul>
            
            <center>
                <a href="{{ config('app.url') }}" class="button">Visit Dashboard</a>
            </center>
            
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions, feel free to contact our support team at support@roundtable.co.za</p>
            
            <p>Best regards,<br><strong>The RoundTable Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} RoundTable Partnership Platform. All rights reserved.</p>
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
