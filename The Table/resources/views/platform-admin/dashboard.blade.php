<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Platform Admin - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-crown"></i> RoundTable Platform Admin</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user-shield"></i> {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Platform Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Users</h6>
                                <h2 class="mb-0">{{ $totalUsers }}</h2>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up"></i> {{ $newUsersThisMonth }} this month
                                </small>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Cohorts</h6>
                                <h2 class="mb-0">{{ $totalCohorts }}</h2>
                                <small class="text-info">
                                    {{ $activeCohorts }} active
                                </small>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-layer-group fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Invested</h6>
                                <h2 class="mb-0">R{{ number_format($totalInvested) }}</h2>
                                <small class="text-success">
                                    Across all cohorts
                                </small>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-chart-line fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Platform Fee</h6>
                                <h2 class="mb-0">R{{ number_format($platformFees) }}</h2>
                                <small class="text-success">
                                    Total collected
                                </small>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Growth Chart -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-area"></i> Platform Growth</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="growthChart" height="80"></canvas>
                    </div>
                </div>

                <!-- All Cohorts -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> All Cohorts</h5>
                        <a href="{{ route('cohorts.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Cohort
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cohort</th>
                                        <th>Admin</th>
                                        <th>Status</th>
                                        <th>Members</th>
                                        <th>Value</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cohorts as $cohort)
                                    <tr>
                                        <td>
                                            <strong>{{ $cohort->name }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($cohort->description, 50) }}</small>
                                        </td>
                                        <td>{{ $cohort->admin->name ?? 'N/A' }}</td>
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
                                        <td colspan="6" class="text-center text-muted">No cohorts yet</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $cohorts->links() }}
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- System Health -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-heartbeat"></i> System Health</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>KYC Approval Rate</span>
                                <span class="text-success">{{ number_format($kycApprovalRate, 1) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $kycApprovalRate }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Active Cohorts</span>
                                <span class="text-info">{{ number_format(($activeCohorts / max($totalCohorts, 1)) * 100, 1) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: {{ ($activeCohorts / max($totalCohorts, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Investment Rate</span>
                                <span class="text-primary">{{ number_format($investmentRate, 1) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ $investmentRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-bell"></i> Recent Activity</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @forelse($recentActivity as $activity)
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            <div>{{ $activity->description }}</div>
                        </div>
                        @empty
                        <p class="text-muted text-center">No recent activity</p>
                        @endforelse
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Admin Tools</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('platform-admin.users') }}" class="btn btn-outline-primary">
                                <i class="fas fa-users-cog"></i> Manage Users
                            </a>
                            <a href="{{ route('platform-admin.kyc') }}" class="btn btn-outline-warning">
                                <i class="fas fa-id-card"></i> KYC Approvals ({{ $pendingKyc }})
                            </a>
                            <a href="{{ route('platform-admin.reports') }}" class="btn btn-outline-info">
                                <i class="fas fa-file-alt"></i> Generate Reports
                            </a>
                            <a href="{{ route('platform-admin.settings') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-cog"></i> Platform Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Growth Chart
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Users',
                    data: {!! json_encode($userGrowth) !!},
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Cohorts',
                    data: {!! json_encode($cohortGrowth) !!},
                    borderColor: 'rgb(118, 75, 162)',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
