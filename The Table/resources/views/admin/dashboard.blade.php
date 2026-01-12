<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 1rem 1.5rem;
            border-radius: 0;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-shield-alt"></i> RoundTable Admin</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user-tie"></i> {{ Auth::user()->name }} (Admin)
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('cohorts.index') }}">
                            <i class="fas fa-layer-group"></i> My Cohorts
                        </a>
                        <a class="nav-link" href="{{ route('admin.members') }}">
                            <i class="fas fa-users"></i> Manage Members
                        </a>
                        <a class="nav-link" href="{{ route('admin.kyc') }}">
                            <i class="fas fa-id-card"></i> KYC Verification
                        </a>
                        <a class="nav-link" href="{{ route('admin.investments') }}">
                            <i class="fas fa-chart-line"></i> Investments
                        </a>
                        <a class="nav-link" href="{{ route('admin.distributions.index') }}">
                            <i class="fas fa-hand-holding-usd"></i> Distributions
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4"><i class="fas fa-chart-pie"></i> Admin Dashboard</h2>

                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">My Cohorts</h6>
                                        <h3 class="mb-0">{{ $myCohorts->count() }}</h3>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-layer-group fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Members</h6>
                                        <h3 class="mb-0">{{ $totalMembers }}</h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="fas fa-users fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Invested</h6>
                                        <h3 class="mb-0">R{{ number_format($totalInvested) }}</h3>
                                    </div>
                                    <div class="text-info">
                                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Pending KYC</h6>
                                        <h3 class="mb-0">{{ $pendingKyc }}</h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-id-card fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-layer-group"></i> My Cohorts Overview</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cohort Name</th>
                                                <th>Status</th>
                                                <th>Members</th>
                                                <th>Total Value</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($myCohorts as $cohort)
                                            <tr>
                                                <td><strong>{{ $cohort->name }}</strong></td>
                                                <td>
                                                    <span class="badge 
                                                        @if($cohort->status == 'open') bg-success
                                                        @elseif($cohort->status == 'active') bg-info
                                                        @else bg-secondary
                                                        @endif">
                                                        {{ ucfirst($cohort->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $cohort->members_count }}/{{ $cohort->max_members }}</td>
                                                <td>R{{ number_format($cohort->contribution_amount * $cohort->members_count) }}</td>
                                                <td>
                                                    <a href="{{ route('cohorts.show', $cohort) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('cohorts.edit', $cohort) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No cohorts yet</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Recent Transactions</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                                <td>{{ ucfirst($transaction->type) }}</td>
                                                <td>{{ $transaction->user->name }}</td>
                                                <td>R{{ number_format($transaction->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No transactions yet</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Pending KYC -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-id-card"></i> Pending KYC</h5>
                            </div>
                            <div class="card-body">
                                @forelse($pendingKycUsers as $user)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <div><strong>{{ $user->name }}</strong></div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <a href="{{ route('admin.kyc.review', $user) }}" class="btn btn-sm btn-primary">
                                        Review
                                    </a>
                                </div>
                                @empty
                                <p class="text-muted text-center">No pending KYC</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('cohorts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create New Cohort
                                    </a>
                                    <a href="{{ route('admin.members') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus"></i> Add Member
                                    </a>
                                    <a href="{{ route('admin.investments.create') }}" class="btn btn-outline-success">
                                        <i class="fas fa-chart-line"></i> Record Investment
                                    </a>
                                    <a href="{{ route('admin.distributions.create') }}" class="btn btn-outline-info">
                                        <i class="fas fa-hand-holding-usd"></i> Create Distribution
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
