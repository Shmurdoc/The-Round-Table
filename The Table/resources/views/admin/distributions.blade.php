<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Distributions - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-shield-alt"></i> RoundTable Admin</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
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
                <h2><i class="fas fa-hand-holding-usd"></i> Manage Distributions</h2>
                <p class="text-muted">Create and track profit distributions to cohort members</p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createDistributionModal">
                    <i class="fas fa-plus"></i> New Distribution
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Distributed</h6>
                        <h3 class="text-success">R{{ number_format($totalDistributed) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Pending</h6>
                        <h3 class="text-warning">R{{ number_format($pendingDistributions) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">This Month</h6>
                        <h3 class="text-info">R{{ number_format($thisMonthDistributions) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Avg per Member</h6>
                        <h3 class="text-primary">R{{ number_format($avgPerMember) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distributions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0"><i class="fas fa-list"></i> All Distributions</h5>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="row g-2">
                            <div class="col-auto">
                                <select name="cohort_id" class="form-select form-select-sm">
                                    <option value="">All Cohorts</option>
                                    @foreach($cohorts as $cohort)
                                    <option value="{{ $cohort->id }}" {{ request('cohort_id') == $cohort->id ? 'selected' : '' }}>
                                        {{ $cohort->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Cohort</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                                <th>Per Member</th>
                                <th>Members</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($distributions as $distribution)
                            <tr>
                                <td>{{ $distribution->scheduled_date ? $distribution->scheduled_date->format('M d, Y') : 'N/A' }}</td>
                                <td><strong>{{ $distribution->cohort->name }}</strong></td>
                                <td>
                                    <span class="badge 
                                        @if($distribution->type == 'periodic') bg-success
                                        @elseif($distribution->type == 'final') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($distribution->type) }}
                                    </span>
                                </td>
                                <td>R{{ number_format($distribution->total_amount / 100, 2) }}</td>
                                <td>R{{ $distribution->cohort->members_count > 0 ? number_format($distribution->total_amount / 100 / $distribution->cohort->members_count, 2) : '0.00' }}</td>
                                <td>{{ $distribution->cohort->members_count }}</td>
                                <td>
                                    <span class="badge 
                                        @if($distribution->status == 'completed') bg-success
                                        @elseif($distribution->status == 'pending') bg-warning
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst($distribution->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.distributions.show', $distribution) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($distribution->status == 'pending')
                                    <form action="{{ route('admin.distributions.complete', $distribution) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Process this distribution? This will trigger payments.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No distributions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($distributions->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $distributions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Distribution Modal -->
    <div class="modal fade" id="createDistributionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.distributions.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Create New Distribution</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Cohort <span class="text-danger">*</span></label>
                            <select name="cohort_id" class="form-select" required onchange="updateCohortInfo(this)">
                                <option value="">Choose cohort...</option>
                                @foreach($cohorts as $cohort)
                                <option value="{{ $cohort->id }}" 
                                        data-members="{{ $cohort->members_count }}"
                                        data-pool="{{ $cohort->contribution_amount * $cohort->members_count }}">
                                    {{ $cohort->name }} ({{ $cohort->members_count }} members)
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="cohortInfo" class="alert alert-info d-none">
                            <strong>Cohort Details:</strong>
                            <br>Members: <span id="memberCount">0</span>
                            <br>Total Pool: R<span id="totalPool">0</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Distribution Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="profit">Profit Distribution</option>
                                <option value="dividend">Dividend</option>
                                <option value="return">Capital Return</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Distribution Amount (R) <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" class="form-control" 
                                   step="0.01" min="0" required 
                                   onchange="calculatePerMember()">
                            <small class="text-muted">Total amount to be distributed to all members</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount Per Member (R)</label>
                            <input type="text" id="perMemberAmount" class="form-control" readonly>
                            <small class="text-muted">Automatically calculated based on total amount</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Distribution Date <span class="text-danger">*</span></label>
                            <input type="date" name="distribution_date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Optional notes about this distribution"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Distribution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCohortInfo(select) {
            const option = select.options[select.selectedIndex];
            if (option.value) {
                const members = option.dataset.members;
                const pool = option.dataset.pool;
                document.getElementById('memberCount').textContent = members;
                document.getElementById('totalPool').textContent = new Intl.NumberFormat().format(pool);
                document.getElementById('cohortInfo').classList.remove('d-none');
                calculatePerMember();
            } else {
                document.getElementById('cohortInfo').classList.add('d-none');
            }
        }

        function calculatePerMember() {
            const cohortSelect = document.querySelector('select[name="cohort_id"]');
            const totalAmount = document.querySelector('input[name="total_amount"]').value;
            
            if (cohortSelect.value && totalAmount) {
                const option = cohortSelect.options[cohortSelect.selectedIndex];
                const members = parseInt(option.dataset.members);
                const perMember = parseFloat(totalAmount) / members;
                document.getElementById('perMemberAmount').value = 'R' + perMember.toFixed(2);
            }
        }

        function viewDistribution(id) {
            // Redirect to distribution details page
            window.location.href = `/admin/distributions/${id}`;
        }
    </script>
</body>
</html>
