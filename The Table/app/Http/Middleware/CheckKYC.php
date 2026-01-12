<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckKYC
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isKYCVerified()) {
            return redirect()->route('kyc.form')
                ->with('warning', 'Please complete KYC verification to access this feature.');
        }

        return $next($request);
    }
}
