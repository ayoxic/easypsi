<!DOCTYPE html>
<html lang="{{ $htmlLang ?? 'fr' }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'EasyPsi' }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo.jpeg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('styles.css') }}?v={{ filemtime(public_path('styles.css')) }}">
        @if (config('services.recaptcha.enabled') && config('services.recaptcha.site_key'))
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    </head>
    <body class="{{ $bodyClass ?? '' }}">
        @yield('content')

        <script src="{{ asset('app.js') }}?v={{ filemtime(public_path('app.js')) }}"></script>
    </body>
</html>
