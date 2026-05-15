<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf" content="{{ csrf_token() }}">
    <title>@yield('title', 'KoreSearch') — Learn & Grow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

@include('partials.header')

<div class="page-wrapper">

    @yield('content')

</div>

<div id="toastContainer" class="toast-container"
     data-flash="{{ json_encode(array_filter([
        'success' => session('success'),
        'error'   => session('error'),
        'info'    => session('info'),
        'warning' => session('warning'),
    ])) }}">
</div>

@include('partials.footer')

@stack('scripts')
</body>
</html>
