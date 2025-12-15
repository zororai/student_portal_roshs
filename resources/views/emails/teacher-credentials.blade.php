<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Teacher Account Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .credentials { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6; }
        .credentials p { margin: 10px 0; }
        .credentials strong { color: #1f2937; }
        .warning { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .warning p { margin: 0; color: #92400e; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p>Your Teacher Account Has Been Created</p>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $name }}</strong>,</p>
            
            <p>Your teacher account has been successfully created. Below are your login credentials:</p>
            
            <div class="credentials">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
            </div>
            
            <div class="warning">
                <p><strong>⚠️ Important:</strong> For security reasons, you will be required to change your password when you first log in. Please keep your new password safe and do not share it with anyone.</p>
            </div>
            
            <p>You can access the portal at:</p>
            <a href="{{ url('/login') }}" class="btn">Login to Portal</a>
            
            <p style="margin-top: 30px;">If you have any questions, please contact the school administration.</p>
            
            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
