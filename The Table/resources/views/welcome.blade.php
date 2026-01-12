<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>RoundTable - Cooperative Partnership Platform</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.min.css') }}" rel="stylesheet" />
</head>

<body class="">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid px-0">
                        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="/">RoundTable</a>
                        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation">
                            <span class="navbar-toggler-icon mt-2">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </span>
                        </button>
                        <div class="collapse navbar-collapse" id="navigation">
                            <ul class="navbar-nav mx-auto ms-xl-auto me-xl-7">
                                <li class="nav-item">
                                    <a class="nav-link me-2" href="{{ route('cohorts.index') }}">
                                        <i class="fa fa-users opacity-6 me-1"></i>Browse Cohorts
                                    </a>
                                </li>
                            </ul>
                            <ul class="navbar-nav d-lg-block d-none">
                                @auth
                                    <li class="nav-item">
                                        <a href="{{ route('member.dashboard') }}" class="btn btn-sm mb-0 me-1 bg-gradient-success">Dashboard</a>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a href="{{ route('login') }}" class="btn btn-sm mb-0 me-1 btn-outline-primary">Sign In</a>
                                        <a href="{{ route('register') }}" class="btn btn-sm mb-0 bg-gradient-success">Sign Up</a>
                                    </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <header>
        <div class="page-header min-vh-100">
            <div class="oblique position-absolute top-0 h-100 d-md-block d-none">
                <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('{{ asset('assets/img/curved-images/curved6.jpg') }}')"></div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-7 d-flex justify-content-center flex-column">
                        <h1 class="text-gradient text-primary mb-0">RoundTable</h1>
                        <h1 class="mb-4">Cooperative Partnership Platform</h1>
                        <p class="lead pe-5 me-5">Join a community of partners pooling resources to achieve financial goals together. Transparent, democratic, and designed for collective success.</p>
                        <div class="buttons">
                            <a href="{{ route('register') }}" class="btn bg-gradient-primary mt-4">Get Started</a>
                            <a href="{{ route('cohorts.index') }}" class="btn btn-outline-secondary mt-4 ms-2">Browse Cohorts</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="pt-5 pb-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex">
                                <div>
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="ni ni-money-coins text-lg opacity-10"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1 text-dark text-sm">R3,000 - R100,000</h6>
                                    <span class="text-sm">Flexible Contributions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex">
                                <div>
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1 text-dark text-sm">Pro-Rata Returns</h6>
                                    <span class="text-sm">Fair Distributions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex">
                                <div>
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="ni ni-paper-diploma text-lg opacity-10"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1 text-dark text-sm">KYC Verified</h6>
                                    <span class="text-sm">Secure Platform</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex">
                                <div>
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-books text-lg opacity-10"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1 text-dark text-sm">Transparent</h6>
                                    <span class="text-sm">Full Reporting</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h3 class="text-gradient text-primary">How It Works</h3>
                    <p class="lead">Join a cohort, contribute collectively, and share in the success</p>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-lg-4">
                    <div class="info-horizontal bg-gradient-primary border-radius-xl d-block d-md-flex p-4">
                        <div class="ps-0 ps-md-3 mt-3 mt-md-0">
                            <h5 class="text-white">1. Join a Cohort</h5>
                            <p class="text-white">Browse investment opportunities and find a community that matches your goals.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="info-horizontal bg-gradient-primary border-radius-xl d-block d-md-flex p-4">
                        <div class="ps-0 ps-md-3 mt-3 mt-md-0">
                            <h5 class="text-white">2. Contribute Together</h5>
                            <p class="text-white">Pool resources with verified partners. Contribute R3,000 to R100,000.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="info-horizontal bg-gradient-primary border-radius-xl d-block d-md-flex p-4">
                        <div class="ps-0 ps-md-3 mt-3 mt-md-0">
                            <h5 class="text-white">3. Share Success</h5>
                            <p class="text-white">Receive pro-rata distributions, vote on decisions, track ROI in real-time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-7 bg-gradient-dark position-relative overflow-hidden">
        <img src="{{ asset('assets/img/shapes/waves-white.svg') }}" alt="pattern" class="position-absolute start-0 top-0 w-100 opacity-6">
        <div class="container position-relative z-index-2">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="text-white">Ready to start building wealth together?</h2>
                    <p class="text-white lead">Join RoundTable today and discover cooperative partnerships.</p>
                    <a href="{{ route('register') }}" class="btn btn-lg btn-white mt-4">Create Your Account</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer pt-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <p class="text-sm my-4">© {{ date('Y') }} RoundTable. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}"></script>
</body>
</html>
