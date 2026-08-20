<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
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

        .alert-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
            padding: 16px;
            border-radius: 6px;
            font-size: 14px;
            margin: 24px 0;
        }

        .summary-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #374151;
        }

        .summary-row {
            margin-bottom: 8px;
        }

        .summary-row strong {
            color: #111827;
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
                            <h1 class="heading">Payment Failed</h1>

                            <p class="paragraph">
                                We were unable to process your recent payment. As a result, your transaction
                                could not be completed.
                            </p>

                            <div class="alert-box">
                                No charges were made to your account. This issue may have occurred due to
                                insufficient funds, an expired card, or a temporary problem with the payment provider.
                            </div>

                            <div class="summary-box">
                                <div class="summary-row">
                                    <strong>Order ID:</strong> DOC-123456
                                </div>
                                <div class="summary-row">
                                    <strong>Document:</strong> Legal Agreement Template
                                </div>
                                <div class="summary-row">
                                    <strong>Amount:</strong> $49.00
                                </div>
                                <div class="summary-row">
                                    <strong>Payment Method:</strong> Online Payment
                                </div>
                            </div>

                            <p class="paragraph">
                                Please update your payment details or try again to complete your purchase.
                            </p>

                            <div class="button-wrapper">
                                <a href="#" class="action-button">Retry Payment</a>
                            </div>

                            <p class="paragraph">
                                If the issue persists or you need assistance, our support team is always available
                                to help you.
                            </p>

                            <p class="paragraph">
                                Regards,<br>
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
