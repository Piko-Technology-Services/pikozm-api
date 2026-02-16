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

    <h2>Thank You, {{ $payment->full_name }}!</h2>
    <p>
        We are thrilled to confirm that we have received your Payment of 
        <span class="highlight">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span> 
        for the project <span class="highlight">{{ $payment->purpose }}</span>.
    </p>
    

    <p class="footer">
        This email confirms your Payment through PikoPay. If you have any questions, contact us at support@pikozm.com.
    </p>
</div>

</body>
</html>
