<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Portfolio - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-users"></i> RoundTable</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('cohorts.index') }}" class="btn btn-outline-light btn-sm me-2">Browse Cohorts</a>
                <span class="navbar-text text-white me-3">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-briefcase"></i> My Partnership Portfolio</h2>
                <p class="text-muted">Track your cooperative partnerships and returns</p>
            </div>
        </div>

        <!-- KYC Alert -->
        @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>KYC Verification Required!</strong>
            @if(Auth::user()->kyc_status === 'pending')
                Your KYC submission is under review. You'll be notified once approved.
            @else
                Please complete your KYC verification to participate in partnerships.
                <a href="{{ route('kyc.submit') }}" class="alert-link">Submit KYC Now</a>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Portfolio Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Invested</h6>
                        <h2 class="text-primary">R{{ number_format($totalInvested) }}</h2>
                        <small class="text-muted">Across {{ $userCohorts->count() }} cohorts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Returns</h6>
                        <h2 class="text-success">R{{ number_format($totalReturns) }}</h2>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> {{ number_format($returnRate, 2) }}% ROI
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Active Cohorts</h6>
                        <h2 class="text-info">{{ $activeCohorts }}</h2>
                        <small class="text-muted">Currently generating returns</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Pending Distributions</h6>
                        <h2 class="text-warning">R{{ number_format($pendingDistributions) }}</h2>
                        <small class="text-muted">To be received</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- My Cohorts -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> My Cohorts</h5>
                    </div>
                    <div class="card-body">
                        @forelse($userCohorts as $cohort)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5>{{ $cohort->name }}</h5>
                                        <p class="text-muted mb-2">{{ Str::limit($cohort->description, 80) }}</p>
                                        <span class="badge 
                                            @if($cohort->status == 'open') bg-success
                                            @elseif($cohort->status == 'active') bg-info
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($cohort->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <small class="text-muted">My Contribution</small>
                                        <h4 class="mb-0">R{{ number_format($cohort->contribution_amount) }}</h4>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('cohorts.show', $cohort) }}" class="btn btn-primary">
                                            View Details <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You haven't joined any cohorts yet.
                            <a href="{{ route('cohorts.index') }}" class="alert-link">Browse available cohorts</a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Transaction History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Cohort</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($transaction->type == 'contribution')
                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> Contribution</span>
                                            @else
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> Distribution</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->cohort->name }}</td>
                                        <td>
                                            @if($transaction->type == 'contribution')
                                            <span class="text-danger">-R{{ number_format($transaction->amount, 2) }}</span>
                                            @else
                                            <span class="text-success">+R{{ number_format($transaction->amount, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($transaction->status == 'completed') bg-success
                                                @elseif($transaction->status == 'pending') bg-warning
                                                @else bg-danger
                                                @endif">
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
                        @if($transactions->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $transactions->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Investment Breakdown -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Investment Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="portfolioChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming Distributions -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Upcoming Distributions</h5>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingDistributions as $distribution)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $distribution->cohort->name }}</strong>
                                    <br><small class="text-muted">{{ $distribution->distribution_date->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">R{{ number_format($distribution->amount_per_member) }}</strong>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center">No upcoming distributions</p>
                        @endforelse
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Average ROI</span>
                                <span class="text-success">{{ number_format($returnRate, 2) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ min($returnRate, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Portfolio Diversity</span>
                                <span class="text-info">{{ $userCohorts->count() }} cohorts</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: {{ min($userCohorts->count() * 20, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('cohorts.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Join More Cohorts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Portfolio Breakdown Chart
        const ctx = document.getElementById('portfolioChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(237, 100, 166, 0.8)',
                        'rgba(255, 154, 158, 0.8)',
                        'rgba(250, 208, 196, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>
