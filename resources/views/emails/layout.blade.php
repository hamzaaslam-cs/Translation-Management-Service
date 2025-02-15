<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        /* General Email Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 20px 0;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #eaeaea;
        }
        .email-header, .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #d6e0eb;
            color: white;
        }
        .email-body {
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #d6e0eb;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer-text {
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-content">
        <!-- Header -->
        <div class="email-header">
            <h1>@yield('header')</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">@yield('footer-text', 'Thank you for using our service!')</p>
        </div>
    </div>
</div>
</body>
</html>
