<!-- resources/views/emails/reset_password.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
    <h2>{{ $heading }}</h2>
    <p>{{ $body }}</p>
    <a href="{{ $resetLink }}" style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;">
        {{ $buttonText ?? 'Reset Password' }}
    </a>
    <p>{{ $footer }}</p>
</body>
</html>
