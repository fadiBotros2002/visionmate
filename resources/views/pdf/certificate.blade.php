<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            border: 10px solid #d4af37;
            padding: 50px;
            background: #fffdf7;
        }
        h1 {
            font-size: 50px;
            color: #d4af37;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 36px;
            margin: 20px 0;
            color: #2c3e50;
        }
        p {
            font-size: 22px;
            margin-bottom: 30px;
            color: #333;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
            padding-right: 100px;
            font-size: 20px;
        }
        .date {
            margin-top: 40px;
            font-size: 20px;
            color: #555;
        }
        .logo {
            width: 120px;
            margin-bottom: 30px;
        }
        .certificate-type {
            font-size: 26px;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <img src="{{ public_path('storage/logo.png') }}" class="logo" alt="logo">
    <h1>Certificate of Appreciation</h1>
    <h2>This certificate is proudly presented to:</h2>
    <h2 style="color:#d4af37;">{{ $volunteer->username }}</h2>
    <p>In recognition of your valuable contribution and dedication in supporting visually impaired individuals.</p>
    <p class="certificate-type">Certificate Level: <strong>{{ ucfirst($certificate_type) }}</strong></p>
    <div class="date">Issued on: {{ $date }}</div>
    <div class="signature">
        _______________________<br>
        Platform Team
    </div>
</body>
</html>
