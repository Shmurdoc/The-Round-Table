@extends('layouts.app')

@section('title', 'Dashboard - RoundTable')

@section('breadcrumb')
    <li class="breadcrumb-item text-sm"><a href="javascript:;">Pages</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
@endsection

@section('page-title', 'Dashboard')

@section('sidebar-menu')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('member.dashboard') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('cohorts.index') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-bullet-list-67 text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Browse Cohorts</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('member.cohorts') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-collection text-info text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">My Cohorts</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('member.notifications') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-bell-55 text-danger text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Notifications</span>
        </a>
    </li>
    <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('kyc.form') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Profile & KYC</span>
        </a>
    </li>
@endsection

@section('content')
<!-- KYC Alert -->
@if(!auth()->user()->isKYCVerified())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                <span class="alert-text">
                    <strong>KYC Verification Required!</strong> 
                        Complete your KYC verification to join cohorts and participate in partnerships.
                </span>
                <a href="{{ route('kyc.form') }}" class="btn btn-sm btn-white ms-auto">Complete KYC</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

<!-- Stats Row -->
<div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Contributed</p>
                            <h5 class="font-weight-bolder mb-0">
                                R{{ number_format(auth()->user()->totalInvestedCapital() / 100, 2) }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Cohorts</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ auth()->user()->activeCohorts()->count() }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Distributions</p>
                            <h5 class="font-weight-bolder mb-0">
                                R{{ number_format(auth()->user()->cohortMemberships->sum('total_distributions_received') / 100, 2) }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                            <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending Votes</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ auth()->user()->notifications()->unread()->where('notification_type', 'vote_created')->count() }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                            <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Cohorts -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>My Cohorts</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                @if(auth()->user()->cohortMemberships->count() > 0)
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cohort</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contribution</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ownership</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ROI</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->cohortMemberships()->with('cohort')->get() as $membership)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $membership->cohort->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $membership->cohort->cohort_id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-{{ $membership->cohort->status === 'operational' ? 'success' : ($membership->cohort->status === 'funding' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($membership->cohort->status) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs font-weight-bold">R{{ number_format($membership->capital_paid / 100, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ number_format($membership->ownership_percentage, 2) }}%</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php $roi = $membership->getROI(); @endphp
                                            <span class="text-xs font-weight-bold text-{{ $roi >= 0 ? 'success' : 'danger' }}">
                                                {{ $roi >= 0 ? '+' : '' }}{{ number_format($roi, 2) }}%
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('member.cohorts.show', $membership->cohort) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View cohort">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center">
                        <p class="text-sm mb-3">You haven't joined any cohorts yet.</p>
                        <a href="{{ route('cohorts.index') }}" class="btn btn-sm btn-primary">Browse Cohorts</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Recent Notifications</h6>
            </div>
            <div class="card-body p-3">
                @forelse(auth()->user()->notifications()->orderBy('created_at', 'desc')->take(5)->get() as $notification)
                    <div class="timeline timeline-one-side">
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-{{ $notification->getPriorityColor() }}">
                                <i class="{{ $notification->getIconClass() }} text-white"></i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $notification->title }}</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                <p class="text-sm mt-2 mb-0">
                                    {{ $notification->message }}
                                </p>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="text-primary text-sm">
                                        {{ $notification->action_text ?? 'View' }} <i class="fas fa-arrow-right text-xs ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-center">No notifications yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-group">
                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                <i class="ni ni-world-2 text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Browse Cohorts</h6>
                                <span class="text-xs">Discover partnership opportunities</span>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('cohorts.index') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto">
                                <i class="ni ni-bold-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                <i class="ni ni-single-02 text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">Update Profile</h6>
                                <span class="text-xs">Manage your account details</span>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('kyc.form') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto">
                                <i class="ni ni-bold-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                <i class="ni ni-bell-55 text-white opacity-10"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark text-sm">View Notifications</h6>
                                <span class="text-xs">Check latest updates</span>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('member.notifications') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto">
                                <i class="ni ni-bold-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush
