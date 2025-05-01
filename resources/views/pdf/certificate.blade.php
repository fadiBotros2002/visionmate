<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            border: 8px solid #0074D9;
            padding: 30px;
            background: #f2f2f2;
            color: #333;
        }
        h1 {
            font-size: 42px;
            color: #0074D9;
            margin-bottom: 5px;
            font-weight: bold;
        }
        h2 {
            font-size: 30px;
            margin: 15px 0;
            color: #444;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
            color: #666;
        }
        .signature {
            margin-top: 25px;
            text-align: right;
            padding-right: 80px;
            font-size: 16px;
            color: #444;
            font-style: italic;
        }
        .date {
            margin-top: 30px;
            font-size: 16px;
            color: #555;
            font-weight: bold;
        }
        .logo {
            width: 230px;
            display: block;
            margin: 0 auto 10px auto;
        }
        .certificate-type {
            font-size: 20px;
            color: #0074D9;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>

</head>
<body>
    <img src="{{ public_path('storage/logo.png') }}" class="logo" alt="logo">
    <h1>Certificate of Appreciation</h1>
    <h2>This certificate is proudly presented to:</h2>
    <h2 style="color:#0074D9;">{{ $volunteer->username }}</h2> <!-- Updated name color to blue -->
    <p>In recognition of your valuable contribution and dedication in supporting visually impaired individuals.</p>
    <p class="certificate-type">Certificate Level: <strong>{{ ucfirst($certificate_type) }}</strong></p>
    <div class="date">Issued on: {{ $date }}</div>
    <div class="signature">
        _______________________<br>
        Vision Mate Team
    </div>
</body>
</html>
