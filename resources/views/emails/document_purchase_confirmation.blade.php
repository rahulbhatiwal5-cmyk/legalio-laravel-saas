<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Purchase Confirmation</title>
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
                            <h1 class="heading">Document Purchase Confirmed</h1>

                            <p class="paragraph">
                                Thank you for your purchase. This email confirms that your document order has been
                                successfully completed.
                            </p>

                            <div class="summary-box">
                                <div class="summary-row">
                                    <strong>Document Name:</strong> Legal Agreement Template
                                </div>
                                <div class="summary-row">
                                    <strong>Order ID:</strong> DOC-123456
                                </div>
                                <div class="summary-row">
                                    <strong>Purchase Date:</strong> 15 March 2025
                                </div>
                                <div class="summary-row">
                                    <strong>Payment Method:</strong> Online Payment
                                </div>
                                <div class="summary-row">
                                    <strong>Total Amount:</strong> $49.00
                                </div>
                            </div>

                            <p class="paragraph">
                                You can access and download your purchased document from your Legalio dashboard
                                at any time.
                            </p>

                            <div class="button-wrapper">
                                <a href="#" class="action-button">View Document</a>
                            </div>

                            <p class="paragraph">
                                If you have any questions regarding your purchase, feel free to contact our support team.
                            </p>

                            <p class="paragraph">
                                Thank you for choosing Legalio.<br>
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
