<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center position-relative">


        <!-- Main Content -->
        <div class="row w-100">
            <div class="col-12 text-center">
                <!-- 404 Error Code with Rocket -->
                <div class="mb-4 position-relative d-inline-block">
                    <span class="display-1 fw-bold text-dark me-2" style="font-size: 12rem; line-height: 1;">4</span>
                    <div class="d-inline-block position-relative">
                        <span class="display-1 fw-bold" style="font-size: 12rem; line-height: 1;color: #012555 !important;">0</span>
                         </div>
                    <span class="display-1 fw-bold text-dark ms-2" style="font-size: 12rem; line-height: 1;">4</span>
                </div>

                <!-- Error Title -->
                <h1 class="h2 fw-bold text-secondary mb-4">Oops! Why you're here?</h1>

                <!-- Error Message -->
                <p class="text-muted mb-5 mx-auto lead" style="max-width: 500px;">
                    We are very sorry for inconvenience. It looks like you're trying to access a page that either has been deleted or never existed.
                </p>

                <!-- Back to Home Button -->
                <a href="{{ route('user.home') }}" style="background-color: #012555 !important;color:white;"class="btn btn-lg rounded-pill px-5 py-3 fw-semibold">
                    Back To Home
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
