<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Browse Cohorts - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cohort-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .cohort-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .progress-ring {
            width: 120px;
            height: 120px;
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-users"></i> RoundTable</a>
            <div class="navbar-nav ms-auto">
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
            <div class="col-md-8">
                <h2><i class="fas fa-layer-group"></i> Browse Partnership Cohorts</h2>
                <p class="text-muted">Join cooperative partnership groups and grow together</p>
            </div>
            <div class="col-md-4 text-end">
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'platform_admin')
                <a href="{{ route('cohorts.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Cohort
                </a>
                @endif
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('cohorts.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="min_contribution" class="form-control" placeholder="Min Contribution" value="{{ request('min_contribution') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="max_contribution" class="form-control" placeholder="Max Contribution" value="{{ request('max_contribution') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cohorts Grid -->
        <div class="row">
            @forelse($cohorts as $cohort)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card cohort-card">
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0">{{ $cohort->name }}</h5>
                        <span class="badge 
                            @if($cohort->status == 'open') badge-status bg-success
                            @elseif($cohort->status == 'active') badge-status bg-info
                            @else badge-status bg-secondary
                            @endif">
                            {{ ucfirst($cohort->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">{{ Str::limit($cohort->description, 100) }}</p>
                        
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="text-muted mb-1">Members</h6>
                                    <h4>{{ $cohort->members_count }}/{{ $cohort->max_members }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Contribution</h6>
                                <h4>R{{ number_format($cohort->contribution_amount) }}</h4>
                            </div>
                        </div>

                        <!-- Progress Circle -->
                        <div class="text-center mb-3">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ ($cohort->members_count / $cohort->max_members) * 100 }}%" 
                                     aria-valuenow="{{ ($cohort->members_count / $cohort->max_members) * 100 }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted">{{ number_format(($cohort->members_count / $cohort->max_members) * 100, 1) }}% Full</small>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('cohorts.show', $cohort) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            @if($cohort->status == 'open' && $cohort->members_count < $cohort->max_members)
                                @if(!$cohort->members->contains(Auth::id()))
                                <a href="{{ route('cohorts.join', $cohort) }}" class="btn btn-primary">
                                    <i class="fas fa-handshake"></i> Join Cohort
                                </a>
                                @else
                                <button class="btn btn-success" disabled>
                                    <i class="fas fa-check"></i> Already Joined
                                </button>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="fas fa-calendar"></i> Start: {{ $cohort->start_date->format('M d, Y') }}
                        @if($cohort->admin)
                        <br><i class="fas fa-user-shield"></i> Admin: {{ $cohort->admin->name }}
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No cohorts available</h5>
                    <p>Check back later or contact an administrator</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $cohorts->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
