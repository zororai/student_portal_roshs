<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $teacher->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .qr-card {
            text-align: center;
            padding: 40px;
            border: 3px solid #1e40af;
            border-radius: 20px;
            max-width: 400px;
            background: #fff;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .school-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .qr-image {
            width: 250px;
            height: 250px;
            margin: 0 auto 20px;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
        }
        
        .qr-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .teacher-name {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }
        
        .teacher-title {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .footer-text {
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .badge {
            display: inline-block;
            background: #1e40af;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 0;
            }
            
            .qr-card {
                border: 2px solid #000;
                page-break-inside: avoid;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <div class="school-name">ROSHS</div>
        <div class="school-subtitle">Robert Sobukwe High School</div>
        
        <span class="badge">STAFF ID CARD</span>
        
        <div class="qr-image">
            <img src="{{ asset('storage/' . $teacher->qr_code) }}" alt="QR Code">
        </div>
        
        <div class="teacher-name">{{ $teacher->user->name }}</div>
        <div class="teacher-title">Teacher</div>
        
        <div class="footer-text">
            Scan this QR code to record attendance<br>
            This card is non-transferable
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
