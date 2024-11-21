<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Newsletter</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Lato', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #F9FAFB;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            padding: 40px;
            text-align: center;
            color: #FFFFFF;
        }

        .header img {
            max-width: 100%;
            height: auto;
        }

        .content {
            padding: 30px;
            color: #333;
        }

        .content h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .content p {
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            font-size: 18px;
            color: #FFFFFF;
            background-color: #cf21a9;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .footer {
            padding: 20px;
            text-align: center;
            background-color: #F9FAFB;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            .content h1 {
                font-size: 20px;
            }

            .button {
                font-size: 16px;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logo }}" alt="Weekly Newsletter of {{ $company_name }}">
            <h1>Welcome to Our Newsletter!</h1>
        </div>
        <div class="content">
            <p>Thank you for subscribing to <strong>{{ $company_name }}</strong>! We are excited to share our latest
                discounts and events with you.</p>
            <p>You will receive exclusive updates directly to your inbox. Don't forget to check out our <a
                    href="{{ $url }}" target="_blank">profile page</a> for past and future issues.</p>
            <p>Ready to start shopping? Click the button below!</p>
            <div style="display: flex;align-items: center;justify-content: center">
                <a href="{{ $url }}" class="button" target="_blank">Start Shopping</a>
            </div>
        </div>
        <div class="footer">
            <p>Follow us on social media:</p>
            <p>
                @if ($profile['contact']['facebook'])
                    <a href="{{ $profile['contact']['facebook'] }}" target="_blank">Facebook</a> |
                @endif
                @if ($profile['contact']['instagram'])
                    <a href="{{ $profile['contact']['instagram'] }}" target="_blank">Instagram</a> |
                @endif
                @if ($profile['contact']['youtube'])
                    <a href="{{ $profile['contact']['youtube'] }}" target="_blank">YouTube</a> |
                @endif
                @if ($profile['contact']['pinterest'])
                    <a href="{{ $profile['contact']['pinterest'] }}" target="_blank">Pinterest</a> |
                @endif
                @if ($profile['contact']['tiktok'])
                    <a href="{{ $profile['contact']['tiktok'] }}" target="_blank">Tiktok</a>
                @endif

            </p>
            <p>&copy; {{ date('Y') }} {{ $company_name }}. All rights reserved.</p>
        </div>
    </div>
</body>
