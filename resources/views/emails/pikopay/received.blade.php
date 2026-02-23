<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Received</title>
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

    <h2>New Payment Received</h2>
    <p>A new Payment has been received via PikoPay:</p>
    <ul>
        <li><strong>From:</strong> {{ $payment->full_name }} ({{ $payment->email }})</li>
        <li><strong>Amount:</strong> {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</li>
        <li><strong>Purpose:</strong> {{ $payment->purpose }}</li>
        <li><strong>Reference:</strong> {{ $payment->reference }}</li>
    </ul>

    <p class="footer">
        This email confirms your Payment through PikoPay. If you have any questions, contact us at support@pikozm.com.
    </p>
</div>

</body>
</html>
