<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Stock Excel Reporter' }}</title>
    @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
{{ $slot }}
</body>
</html>
