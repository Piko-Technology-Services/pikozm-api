<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Donation Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.05);
        }
        h2 {
            color: #0a2f5e;
            margin-bottom: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 5px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
        }
        a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0a2f5e;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
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

    <h2>New Donation Received</h2>
    <p>A new donation has been received via Piko Digital Impact:</p>
    <ul>
        <li><strong>Donor:</strong> {{ $donation->name }} ({{ $donation->email }})</li>
        <li><strong>Amount:</strong> {{ $donation->currency }} {{ number_format($donation->amount) }}</li>
        <li><strong>Focus Area:</strong> {{ $donation->focus_area }}</li>
        <li><strong>Reference:</strong> {{ $donation->reference }}</li>
    </ul>
    <a href="{{ url('/dashboard') }}" class="button">View in Dashboard</a>
    <p class="footer">You are receiving this email because you are a management recipient of Piko Digital Impact.</p>
</div>

</body>
</html>
