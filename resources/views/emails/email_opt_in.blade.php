<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Subscription Confirmation</title>
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

        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
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

        .muted-text {
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
                            <h1 class="heading">Subscription Confirmed</h1>

                            <p class="paragraph">
                                You have successfully opted in to receive commercial emails from Legalio.
                            </p>

                            <div class="info-box">
                                You will now receive updates about new documents, feature announcements,
                                offers, and other relevant information related to our services.
                            </div>

                            <p class="paragraph">
                                We respect your privacy and will only send emails that are relevant and useful to you.
                            </p>

                            <div class="button-wrapper">
                                <a href="#" class="action-button">Manage Email Preferences</a>
                            </div>

                            <p class="muted-text">
                                You can update your communication preferences or unsubscribe at any time
                                from your account settings or via the link provided in our emails.
                            </p>

                            <p class="paragraph">
                                Thank you for staying connected,<br>
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
