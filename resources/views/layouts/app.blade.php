<!DOCTYPE html>
<html lang="{{ $htmlLang ?? 'fr' }}" dir="{{ $dir ?? 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'EasyPsi' }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo.jpeg') }}">
        <link rel="stylesheet" href="{{ asset('styles.css') }}?v={{ filemtime(public_path('styles.css')) }}">
        @if (config('services.recaptcha.site_key'))
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    </head>
    <body class="{{ $bodyClass ?? '' }}">
        @yield('content')

        <script src="{{ asset('app.js') }}?v={{ filemtime(public_path('app.js')) }}"></script>
    </body>
</html>
