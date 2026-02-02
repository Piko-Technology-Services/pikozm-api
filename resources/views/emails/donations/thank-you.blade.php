<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You for Your Donation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.05);
        }
        h2 {
            color: #0a2f5e;
        }
        p {
            line-height: 1.6;
        }
        .highlight {
            color: #0a2f5e;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Logo -->
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="https://pikozm.com/images/piko-logo.png" 
             alt="Piko Digital Impact Logo" 
             style="max-width: 150px; height: auto;">
    </div>

    <h2>Thank You, {{ $donation->name }}!</h2>
    <p>
        We sincerely appreciate your generous donation of 
        <span class="highlight">{{ $donation->currency }} {{ number_format($donation->amount) }}</span>.
    </p>
    <p>
        Your support towards <span class="highlight">{{ $donation->focus_area }}</span> is making a real difference.
    </p>

    @if($donation->message)
    <p><em>Your message:</em> “{{ $donation->message }}”</p>
    @endif

    <p><strong>Reference:</strong> {{ $donation->reference }}</p>

    <p>Warm regards,<br>
    <strong>Piko Digital Impact Team</strong></p>

    <p class="footer">
        This email confirms your donation to Piko Digital Impact. If you have any questions, contact us at support@pikozm.com.
    </p>
</div>

</body>
</html>
