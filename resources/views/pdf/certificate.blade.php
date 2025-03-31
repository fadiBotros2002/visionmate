<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            border: 8px solid #b22222;
            padding: 50px;
            background: #f2f2f2;
            color: #333;
        }
        h1 {
            font-size: 48px;
            color: #b22222;
            margin-bottom: 10px;
            font-weight: bold;
        }
        h2 {
            font-size: 34px;
            margin: 20px 0;
            color: #444;
        }
        p {
            font-size: 20px;
            margin-bottom: 30px;
            color: #666;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
            padding-right: 100px;
            font-size: 18px;
            color: #444;
            font-style: italic;
        }
        .date {
            margin-top: 40px;
            font-size: 18px;
            color: #555;
            font-weight: bold;
        }
        .logo {
            width: 100px;
            margin-bottom: 30px;
        }
        .certificate-type {
            font-size: 24px;
            color: #b22222;
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <img src="{{ public_path('storage/logo.png') }}" class="logo" alt="logo">
    <h1>Certificate of Appreciation</h1>
    <h2>This certificate is proudly presented to:</h2>
    <h2 style="color:#b22222;">{{ $volunteer->username }}</h2>
    <p>In recognition of your valuable contribution and dedication in supporting visually impaired individuals.</p>
    <p class="certificate-type">Certificate Level: <strong>{{ ucfirst($certificate_type) }}</strong></p>
    <div class="date">Issued on: {{ $date }}</div>
    <div class="signature">
        _______________________<br>
        Platform Team
    </div>
</body>
</html>
