@extends('layouts.app')

@section('title', 'Withdraw - RoundTable')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Withdraw Funds</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('member.withdraw.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount (ZAR)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="account_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-danger">Submit Withdrawal Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
