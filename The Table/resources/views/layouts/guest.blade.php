<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'RoundTable - Cooperative Investment Platform')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    
    <!-- Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    <!-- Argon Dashboard CSS -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.min.css') }}" rel="stylesheet" />
    
    @stack('styles')
</head>

<body class="">
    <main class="main-content mt-0">
        @yield('content')
    </main>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    
    <!-- Argon Dashboard JS -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
