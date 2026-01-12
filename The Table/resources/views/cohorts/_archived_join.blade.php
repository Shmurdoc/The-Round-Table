<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join Cohort - Payment - RoundTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-users"></i> RoundTable</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient text-white text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h3 class="mb-0"><i class="fas fa-handshake"></i> Join {{ $cohort->name }}</h3>
                    </div>
                    <div class="card-body p-5">
                        <!-- Cohort Summary -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Cohort Details</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $cohort->name }}</p>
                            <p class="mb-1"><strong>Contribution Required:</strong> R{{ number_format($cohort->contribution_amount) }}</p>
                            <p class="mb-1"><strong>Start Date:</strong> {{ $cohort->start_date->format('F d, Y') }}</p>
                            <p class="mb-0"><strong>Members:</strong> {{ $cohort->members_count }}/{{ $cohort->max_members }}</p>
                        </div>

                        <!-- KYC Check -->
                        @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>KYC Verification Required!</strong>
                            <p class="mb-0">You must complete KYC verification before joining a cohort.</p>
                            <a href="{{ route('kyc.submit') }}" class="btn btn-danger btn-sm mt-2">Complete KYC Now</a>
                        </div>
                        @else
                        <!-- Payment Form -->
                        <h5 class="mb-3"><i class="fas fa-credit-card"></i> Payment Details</h5>
                        
                        <form action="{{ route('cohorts.process-payment', $cohort) }}" method="POST" id="paymentForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label">Amount to Pay</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">R</span>
                                    <input type="text" class="form-control" value="{{ number_format($cohort->contribution_amount, 2) }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payfast" value="payfast" checked>
                                    <label class="form-check-label" for="payfast">
                                        <i class="fas fa-credit-card text-primary"></i> PayFast (Credit Card, EFT, Instant EFT)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="manual" value="manual">
                                    <label class="form-check-label" for="manual">
                                        <i class="fas fa-university text-info"></i> Manual Bank Transfer
                                    </label>
                                </div>
                            </div>

                            <!-- PayFast Section -->
                            <div id="payfastSection" class="payment-section">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-shield-alt"></i> Secure Payment via PayFast</h6>
                                    <p class="mb-0 small">You will be redirected to PayFast's secure payment gateway to complete your transaction.</p>
                                </div>
                            </div>

                            <!-- Manual Transfer Section -->
                            <div id="manualSection" class="payment-section d-none">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-university"></i> Bank Transfer Details</h6>
                                        <p class="mb-1"><strong>Bank:</strong> FNB</p>
                                        <p class="mb-1"><strong>Account Name:</strong> RoundTable Investment Platform</p>
                                        <p class="mb-1"><strong>Account Number:</strong> 62847291049</p>
                                        <p class="mb-1"><strong>Branch Code:</strong> 250655</p>
                                        <p class="mb-3"><strong>Reference:</strong> {{ Auth::user()->id }}-COH{{ $cohort->id }}</p>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Upload Proof of Payment</label>
                                            <input type="file" name="proof_of_payment" class="form-control" accept="image/*,application/pdf">
                                            <small class="text-muted">Upload bank statement/payment confirmation</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-4 mt-4">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the cohort terms and understand that contributions are non-refundable once the cohort becomes active.
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-lock"></i> Proceed to Payment
                                </button>
                                <a href="{{ route('cohorts.show', $cohort) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Cohort
                                </a>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="text-center mt-4 text-muted">
                    <i class="fas fa-lock"></i> <small>All payments are secure and encrypted</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle payment sections
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'payfast') {
                    document.getElementById('payfastSection').classList.remove('d-none');
                    document.getElementById('manualSection').classList.add('d-none');
                } else {
                    document.getElementById('payfastSection').classList.add('d-none');
                    document.getElementById('manualSection').classList.remove('d-none');
                }
            });
        });
    </script>
</body>
</html>
