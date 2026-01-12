<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $cohort->name }} - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-users"></i> RoundTable</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('cohorts.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left"></i> Back to Cohorts
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Cohort Header -->
        <div class="card mb-4">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-0">{{ $cohort->name }}</h2>
                        <p class="mb-0 opacity-75">{{ $cohort->description }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge fs-6
                            @if($cohort->status == 'open') bg-success
                            @elseif($cohort->status == 'active') bg-info
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($cohort->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center border-end">
                        <h6 class="text-muted">Contribution Amount</h6>
                        <h3 class="text-primary">R{{ number_format($cohort->contribution_amount) }}</h3>
                    </div>
                    <div class="col-md-3 text-center border-end">
                        <h6 class="text-muted">Members</h6>
                        <h3 class="text-success">{{ $cohort->members_count }}/{{ $cohort->max_members }}</h3>
                    </div>
                    <div class="col-md-3 text-center border-end">
                        <h6 class="text-muted">Total Pool</h6>
                        <h3 class="text-info">R{{ number_format($cohort->contribution_amount * $cohort->members_count) }}</h3>
                    </div>
                    <div class="col-md-3 text-center">
                        <h6 class="text-muted">Start Date</h6>
                        <h3 class="text-warning">{{ $cohort->start_date->format('M d, Y') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Members List -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Cohort Members</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Email</th>
                                        <th>KYC Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cohort->members as $member)
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-circle text-primary"></i> {{ $member->name }}
                                            @if($member->id == $cohort->admin_user_id)
                                            <span class="badge bg-warning text-dark ms-2">Admin</span>
                                            @endif
                                        </td>
                                        <td>{{ $member->email }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($member->kyc_status == 'approved') bg-success
                                                @elseif($member->kyc_status == 'pending') bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ ucfirst($member->kyc_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $member->pivot->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No members yet</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Partnership Allocations -->
                @if($cohort->investments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Partnership Allocations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                            <th>Partnership</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    @foreach($cohort->investments as $investment)
                                    <tr>
                                        <td>{{ $investment->name }}</td>
                                        <td>{{ $investment->type }}</td>
                                        <td>R{{ number_format($investment->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $investment->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($investment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $investment->investment_date->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Actions Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($cohort->status == 'open' && $cohort->members_count < $cohort->max_members)
                            @if(!$cohort->members->contains(Auth::id()))
                            <div class="d-grid gap-2">
                                <a href="{{ route('cohorts.join', $cohort) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-handshake"></i> Join This Cohort
                                </a>
                            </div>
                            <div class="alert alert-info mt-3 small">
                                <i class="fas fa-info-circle"></i> Joining requires R{{ number_format($cohort->contribution_amount) }} contribution
                            </div>
                            @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> You are a member of this cohort
                            </div>
                            @if(Auth::id() != $cohort->admin_user_id)
                            <div class="d-grid">
                                <form action="{{ route('cohorts.leave', $cohort) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this cohort?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-sign-out-alt"></i> Leave Cohort
                                    </button>
                                </form>
                            </div>
                            @endif
                            @endif
                        @elseif($cohort->status == 'active')
                            <div class="alert alert-info">
                                <i class="fas fa-check"></i> Cohort is active
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <i class="fas fa-lock"></i> Cohort is closed
                            </div>
                        @endif

                        @if(Auth::id() == $cohort->admin_user_id || Auth::user()->role == 'platform_admin')
                        <hr>
                        <h6 class="text-muted">Admin Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('cohorts.edit', $cohort) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit Cohort
                            </a>
                            @if($cohort->status == 'open' && $cohort->members_count >= $cohort->max_members)
                            <form action="{{ route('cohorts.activate', $cohort) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-play"></i> Activate Cohort
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Capacity</small>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ ($cohort->members_count / $cohort->max_members) * 100 }}%">
                                    {{ number_format(($cohort->members_count / $cohort->max_members) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Contributions:</span>
                            <strong>R{{ number_format($cohort->contribution_amount * $cohort->members_count) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Partnerships:</span>
                            <strong>R{{ number_format($cohort->investments->sum('amount')) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Available Balance:</span>
                            <strong class="text-success">
                                R{{ number_format(($cohort->contribution_amount * $cohort->members_count) - $cohort->investments->sum('amount')) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
