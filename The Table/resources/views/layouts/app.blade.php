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

<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('member.dashboard') }}">
                <span class="ms-1 font-weight-bold text-white">RoundTable</span>
            </a>
        </div>
        
        <hr class="horizontal dark mt-0">
        
        <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                @yield('sidebar-menu')
            </ul>
        </div>
        
        <div class="sidenav-footer mx-3 mt-3">
            <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
                <div class="full-background" style="background-image: url('{{ asset('assets/img/curved-images/white-curved.jpg') }}')"></div>
                <div class="card-body text-start p-3 w-100">
                    <div class="docs-info">
                        <h6 class="text-white up mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
                        <p class="text-xs font-weight-bold">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        @yield('breadcrumb')
                    </ol>
                    <h6 class="font-weight-bolder mb-0">@yield('page-title')</h6>
                </nav>
                
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        @yield('navbar-search')
                    </div>
                    
                    <ul class="navbar-nav justify-content-end">
                        <!-- Notifications -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="#" class="nav-link text-body p-0" id="dropdownNotifications" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer"></i>
                                @if(auth()->user()->notifications()->unread()->count() > 0)
                                    <span class="badge badge-sm badge-circle badge-danger">
                                        {{ auth()->user()->notifications()->unread()->count() }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownNotifications">
                                @forelse(auth()->user()->notifications()->unread()->take(5)->get() as $notification)
                                    <li class="mb-2">
                                        <a class="dropdown-item border-radius-md" href="{{ $notification->action_url ?? '#' }}">
                                            <div class="d-flex py-1">
                                                <div class="my-auto">
                                                    <i class="{{ $notification->getIconClass() }} text-{{ $notification->getPriorityColor() }}"></i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center ms-2">
                                                    <h6 class="text-sm font-weight-normal mb-1">
                                                        {{ $notification->title }}
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ Str::limit($notification->message, 50) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="mb-2">
                                        <span class="dropdown-item">No new notifications</span>
                                    </li>
                                @endforelse
                                <li class="text-center">
                                    <a href="{{ route('member.notifications') }}" class="text-xs text-primary">View all</a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- User Menu -->
                        <li class="nav-item d-flex align-items-center">
                            <a href="{{ route('logout') }}" class="nav-link text-body font-weight-bold px-0"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out me-sm-1"></i>
                                <span class="d-sm-inline d-none">Sign Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="container-fluid py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                    <span class="alert-text"><strong>Success!</strong> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                    <span class="alert-text"><strong>Error!</strong> {{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                    <div class="alert-text">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
            
            <!-- Footer -->
            <footer class="footer pt-3">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                © {{ date('Y') }} RoundTable. All rights reserved.
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted">About</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted">Support</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted">Terms</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted">Privacy</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    
    <!-- Argon Dashboard JS -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
