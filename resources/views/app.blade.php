<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Tambahkan di head section -->
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @inertiaHead
        @routes
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>