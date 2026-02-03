<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
        }
        h2 { color: #0a2f5e; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px 0; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #0a2f5e;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align:center">
        <img src="https://pikozm.com/images/piko-logo.png" width="140">
    </div>

    @yield('content')

    <p class="footer">
        You are receiving this email from Piko Digital Impact Programme.
    </p>
</div>
</body>
</html>
