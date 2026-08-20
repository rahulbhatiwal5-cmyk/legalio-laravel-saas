<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Receipt</title>
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

        .receipt-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #374151;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .receipt-row strong {
            color: #111827;
        }

        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 12px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 700;
            margin-top: 12px;
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

            .receipt-row,
            .total-row {
                flex-direction: column;
                gap: 4px;
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
                            <h1 class="heading">Invoice Receipt</h1>

                            <p class="paragraph">
                                Thank you for your payment. This email serves as your official receipt
                                for the completed transaction.
                            </p>

                            <div class="receipt-box">
                                <div class="receipt-row">
                                    <span>Invoice Number</span>
                                    <strong>INV-2025-00123</strong>
                                </div>
                                <div class="receipt-row">
                                    <span>Order ID</span>
                                    <strong>DOC-123456</strong>
                                </div>
                                <div class="receipt-row">
                                    <span>Invoice Date</span>
                                    <strong>15 March 2025</strong>
                                </div>
                                <div class="receipt-row">
                                    <span>Payment Method</span>
                                    <strong>Online Payment</strong>
                                </div>

                                <div class="divider"></div>

                                <div class="receipt-row">
                                    <span>Document Purchase</span>
                                    <strong>$49.00</strong>
                                </div>

                                <div class="total-row">
                                    <span>Total Paid</span>
                                    <span>$49.00</span>
                                </div>
                            </div>

                            <p class="paragraph">
                                You can keep this email for your records. The purchased document is now
                                available in your account.
                            </p>

                            <div class="button-wrapper">
                                <a href="#" class="action-button">View Invoice</a>
                            </div>

                            <p class="paragraph">
                                If you have any questions regarding this invoice, please contact our support team.
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
