<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 40px 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
        }

        .email-content {
            padding: 32px;
        }

        .heading {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .paragraph {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 16px;
            color: #374151;
        }

        .success-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 16px;
            border-radius: 6px;
            font-size: 14px;
            margin: 24px 0;
        }

        .button-wrapper {
            text-align: center;
            margin: 32px 0;
        }

        .action-button {
            display: inline-block;
            background-color: #0b2a4a;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 6px;
        }

        .info-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 24px;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #9ca3af;
            background-color: #f9fafb;
        }

        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 24px;
            }

            .heading {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-container" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="email-content">
                            <h1 class="heading">Registration Successful</h1>

                            <p class="paragraph">
                                Thank you for registering with Legalio. Your account has been successfully created.
                            </p>

                            <div class="success-box">
                                You can now log in and start accessing legal documents and services tailored to your needs.
                            </div>

                            <p class="paragraph">
                                To get started, click the button below and explore your dashboard.
                            </p>

                            <div class="button-wrapper">
                                <a href="#" class="action-button">Go to Dashboard</a>
                            </div>

                            <p class="info-text">
                                If you did not create this account, please contact our support team immediately.
                            </p>

                            <p class="paragraph">
                                Welcome aboard,<br>
                                <strong>The Legalio Team</strong>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer">
                            Legalio, Inc. &nbsp;|&nbsp; 123 Legal Street Miami, FL, USA
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
