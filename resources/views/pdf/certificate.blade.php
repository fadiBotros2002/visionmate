<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            border: 10px solid #f0c040;
            padding: 50px;
            background: #fffef7;
        }
        h1 {
            font-size: 48px;
            color: #d4af37;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 32px;
            margin: 20px 0;
        }
        p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
            padding-right: 100px;
            font-size: 18px;
        }
        .date {
            margin-top: 40px;
            font-size: 18px;
        }
        .logo {
            width: 100px;
            margin-bottom: 30px;
        }
        .certificate-type {
            font-size: 28px;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <img src="{{ public_path('storage/logo.png') }}" class="logo" alt="logo">
    <h1>Certificate of Appreciation</h1>
    <h2>This certificate is proudly presented to:</h2>
    <h2 style="color:#2c3e50;">{{ $volunteer->username }}</h2>
    <p>In recognition of your valuable support and dedication towards helping visually impaired individuals.</p>
    <p class="certificate-type">Certificate Level: <strong>{{ ucfirst($certificate_type) }}</strong></p>
    <div class="date">Awarded on: {{ $date }}</div>
    <div class="signature">
        _______________________<br>
        Platform Team
    </div>
</body>
</html>
