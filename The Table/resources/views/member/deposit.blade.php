@extends('layouts.app')

@section('title', 'Deposit - RoundTable')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Deposit Funds</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('member.deposit.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount (ZAR)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="eft">EFT / Bank Transfer</option>
                                <option value="payfast">PayFast</option>
                                <option value="manual">Manual (Admin)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-success">Submit Deposit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
