<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Reliable ready-mixed concrete for projects across Bacolod City and Negros Occidental.">
    <title>Techtonic Concrete Industries Inc.</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <script>window.__TECHTONIC_CONTENT__ = @json($contentJson);</script>
    <script>window.__TECHTONIC_PAGE_CONTENT__ = @json($pageContent);</script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/main.tsx'])
</head>
<body>
    <div id="root"></div>
</body>
</html>
