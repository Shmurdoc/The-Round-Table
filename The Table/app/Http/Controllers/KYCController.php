<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KYCController extends Controller
{
    /**
     * Show KYC form
     */
    public function form()
    {
        return view('kyc.submit');
    }

    /**
     * Submit KYC documents
     */
    public function submit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'id_number' => 'required|string|max:13',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'id_document_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'id_document_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_of_residence' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'crypto_network' => 'required|in:TRC20,BEP20,ERC20',
            'crypto_wallet_address' => 'required|string|min:20|max:100',
            'account_holder_name' => 'required|string|max:255',
        ]);

        // Upload documents
        $documentPaths = [];
        if ($request->hasFile('id_document_front')) {
            $documentPaths['kyc_id_document_front'] = $request->file('id_document_front')->store('kyc/ids', 'public');
        }
        if ($request->hasFile('id_document_back')) {
            $documentPaths['kyc_id_document_back'] = $request->file('id_document_back')->store('kyc/ids', 'public');
        }
        if ($request->hasFile('proof_of_residence')) {
            $documentPaths['kyc_proof_of_residence'] = $request->file('proof_of_residence')->store('kyc/residence', 'public');
        }

        // Map fields to user model
        $user->update([
            'kyc_id_number' => $validated['id_number'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'crypto_network' => $validated['crypto_network'],
            'crypto_wallet_address' => $validated['crypto_wallet_address'],
            'account_holder_name' => $validated['account_holder_name'],
            'kyc_id_document_front' => $documentPaths['kyc_id_document_front'] ?? null,
            'kyc_id_document_back' => $documentPaths['kyc_id_document_back'] ?? null,
            'kyc_proof_of_residence' => $documentPaths['kyc_proof_of_residence'] ?? null,
            'kyc_status' => 'pending',
            'kyc_submitted_at' => now(),
        ]);

        return redirect()->route('member.portfolio')
            ->with('success', 'KYC documents submitted successfully! We will review and notify you.');
    }

    public function reviewKYC()
    {
        $pendingUsers = User::where('kyc_status', 'pending')->paginate(20);
        return view('admin.kyc', compact('pendingUsers'));
    }

    public function showReview(User $user)
    {
        return view('admin.kyc-review', compact('user'));
    }

    /**
     * Approve KYC for a user
     */
    public function approveKYC(Request $request, User $user)
    {
        $user->update([
            'kyc_status' => 'verified',
            'kyc_verified_at' => now(),
            'kyc_rejection_reason' => null,
        ]);

        $adminName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        ActivityLog::log('kyc_approved', $user->id, null, 'KYC verified by admin ' . $adminName);

        // Create notification for user
        app(\App\Services\NotificationService::class)->notifyKYCApproved($user);

        return redirect()->route('admin.kyc')
            ->with('success', 'KYC approved successfully for ' . $user->first_name . ' ' . $user->last_name);
    }

    /**
     * Reject KYC for a user
     */
    public function rejectKYC(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::log('kyc_rejected', $user->id, null, 'KYC rejected by admin: ' . $validated['rejection_reason']);

        // Create notification for user
        app(\App\Services\NotificationService::class)->notifyKYCRejected($user, $validated['rejection_reason']);

        return redirect()->route('admin.kyc')
            ->with('success', 'KYC rejected for ' . $user->first_name . ' ' . $user->last_name);
    }

    /**
            if ($user->proof_of_address) {
                Storage::delete($user->proof_of_address);
            }
            
            $addressPath = $request->file('proof_of_address')->store('kyc/proof-of-address', 'public');
            $validated['proof_of_address'] = $addressPath;
        }

        // Update KYC status to pending
        $validated['kyc_status'] = 'pending';
        $validated['kyc_submitted_at'] = now();

        $user->update($validated);

        ActivityLog::log('kyc_submitted', $user->id, null, 'User submitted KYC documents for verification');

        return redirect()->route('kyc.form')
            ->with('success', 'KYC documents submitted successfully! Your verification is pending approval.');
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $user->update($validated);

        ActivityLog::log('profile_updated', $user->id, null, 'User updated profile information');

        return redirect()->back()
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update crypto wallet information
     */
    public function updateBanking(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verify KYC is approved before allowing wallet details
        if (!$user->isKYCVerified()) {
            return redirect()->back()
                ->with('error', 'You must complete KYC verification before updating wallet details.');
        }

        $validated = $request->validate([
            'crypto_network' => 'required|in:TRC20,BEP20,ERC20',
            'crypto_wallet_address' => 'required|string|min:20|max:100',
            'account_holder_name' => 'required|string|max:255',
        ]);

        $user->update($validated);

        ActivityLog::log('crypto_wallet_updated', $user->id, null, 'User updated crypto wallet information');

        return redirect()->back()
            ->with('success', 'Crypto wallet details updated successfully!');
    }

    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_method' => 'sms',
            'phone_number' => $request->phone_number,
        ]);

        ActivityLog::log('2fa_enabled', $user->id, null, 'User enabled two-factor authentication');

        return redirect()->back()
            ->with('success', 'Two-factor authentication enabled successfully!');
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_method' => null,
        ]);

        ActivityLog::log('2fa_disabled', $user->id, null, 'User disabled two-factor authentication');

        return redirect()->back()
            ->with('success', 'Two-factor authentication disabled.');
    }
}
