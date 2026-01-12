<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function depositForm()
    {
        return view('member.deposit');
    }

    public function depositSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();

        $amountCents = intval(round($request->input('amount') * 100));

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'direction' => 'inflow',
            'amount' => $amountCents,
            'currency' => 'ZAR',
            'status' => 'pending',
            'payment_method' => $request->input('payment_method'),
            'description' => $request->input('description') ?? 'User deposit request',
        ]);

        return redirect()->route('member.portfolio')->with('success', 'Deposit request submitted. Awaiting confirmation.');
    }

    public function withdrawForm()
    {
        return view('member.withdraw');
    }

    public function withdrawSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
        ]);

        $user = Auth::user();

        $amountCents = intval(round($request->input('amount') * 100));

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'direction' => 'outflow',
            'amount' => $amountCents,
            'currency' => 'ZAR',
            'status' => 'pending',
            'to_account' => $request->input('account_number'),
            'to_account_name' => $request->input('account_name'),
            'bank_name' => $request->input('bank_name'),
            'description' => $request->input('description') ?? 'User withdrawal request',
        ]);

        return redirect()->route('member.portfolio')->with('success', 'Withdrawal request submitted. It will be processed shortly.');
    }
}
