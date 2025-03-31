<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate to VisionMate</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h2>Donate to VisionMate</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('donation.process') }}" method="POST">
        @csrf
        <label>Amount (USD):</label>
        <input type="number" name="amount" required>

        <script
            src="https://checkout.stripe.com/checkout.js"
            class="stripe-button"
            data-key="{{ config('stripe.key') }}"
            data-name="VisionMate"
            data-description="Donate to help the blind"
            data-currency="usd">
        </script>
    </form>
</body>
</html>
