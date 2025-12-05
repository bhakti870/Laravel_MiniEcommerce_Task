<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <!-- Bootstrap (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom frontend CSS (place file at public/css/frontend.css) -->
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@include('frontend.partials.navbar')

<div class="container my-4">
    @include('partials.flash') {{-- optional: flash messages --}}
    @yield('content')
</div>

@include('frontend.partials.footer')

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom frontend JS (place file at public/js/frontend.js) -->
<script src="{{ asset('js/frontend.js') }}"></script>
</body>
</html>
