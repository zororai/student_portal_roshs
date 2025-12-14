<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin: 15px 0;
        }
        .status-pending {
            background: #fef3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .details h3 {
            margin-top: 0;
            color: #667eea;
        }
        .details p {
            margin: 8px 0;
        }
        .details strong {
            color: #555;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
            background: #f9f9f9;
        }
        .message-box {
            background: #e8f4fd;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rose of Sharon High School</h1>
        <p>Application Status Update</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $recipientType === 'student' ? $application->first_name : $application->guardian_full_name }},</p>
        
        <p>We are writing to inform you about the status of the student application for <strong>{{ $application->first_name }} {{ $application->last_name }}</strong>.</p>
        
        <p>Your application status has been updated to:</p>
        
        <div style="text-align: center;">
            @if($application->status === 'pending')
                <span class="status-badge status-pending">⏳ PENDING</span>
            @elseif($application->status === 'approved')
                <span class="status-badge status-approved">✅ APPROVED</span>
            @elseif($application->status === 'rejected')
                <span class="status-badge status-rejected">❌ REJECTED</span>
            @else
                <span class="status-badge status-pending">{{ strtoupper($application->status) }}</span>
            @endif
        </div>
        
        @if($application->status === 'approved')
            <div class="message-box">
                <strong>Congratulations!</strong> Your application has been approved. Please contact the school administration for the next steps in the enrollment process.
            </div>
        @elseif($application->status === 'rejected')
            <div class="message-box">
                We regret to inform you that your application was not successful at this time. If you have any questions, please contact our admissions office.
            </div>
        @else
            <div class="message-box">
                Your application is currently being reviewed. We will notify you once a decision has been made.
            </div>
        @endif
        
        <div class="details">
            <h3>Application Details</h3>
            <p><strong>Student Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</p>
            <p><strong>Applying for:</strong> {{ $application->applying_for_form }}</p>
            @if($application->school_applying_for)
                <p><strong>School:</strong> {{ $application->school_applying_for }}</p>
            @endif
            <p><strong>Application Date:</strong> {{ $application->created_at->format('F j, Y') }}</p>
        </div>
        
        @if($application->admin_notes)
            <div class="details">
                <h3>Admin Notes</h3>
                <p>{{ $application->admin_notes }}</p>
            </div>
        @endif
        
        <p>If you have any questions regarding this application, please do not hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <strong>Rose of Sharon High School</strong><br>
        Admissions Office</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Rose of Sharon High School.</p>
        <p>Please do not reply directly to this email.</p>
    </div>
</body>
</html>
