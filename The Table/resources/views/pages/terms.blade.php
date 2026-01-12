@extends('layouts.modern')

@section('title', 'Terms & Conditions')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Terms & Conditions</h1>
            <p class="text-slate-400">Last updated: January 9, 2026</p>
        </div>

        <!-- Content Card -->
        <div class="bg-slate-800/50 backdrop-blur-xl rounded-3xl border border-slate-700/50 p-8 md:p-12 space-y-8">
            
            <!-- Section 1 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">1</span>
                    Acceptance of Terms
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    By accessing and using the RoundTable Partnership Platform ("Platform"), you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our Platform.
                </p>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">2</span>
                    Partnership Model
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>The Platform facilitates partnership opportunities through cohort-based structures. Key terms:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li><strong class="text-white">Cohorts:</strong> Group partnership structures for asset utilization, leasing, or resale ventures</li>
                        <li><strong class="text-white">Contributions:</strong> Financial participation in cohort activities (minimum R3,000, maximum R100,000)</li>
                        <li><strong class="text-white">Profit Distribution:</strong> 50% to operational partner (admin), 50% distributed pro-rata to contributing partners</li>
                        <li><strong class="text-white">Weekly Distributions:</strong> Profits distributed every Friday at 5:00 PM SAST</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">3</span>
                    KYC Requirements
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>To participate in partnerships, you must complete Know Your Customer (KYC) verification:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li>Valid South African ID document (front and back)</li>
                        <li>Proof of residence (not older than 3 months)</li>
                        <li>Valid USDT wallet address for receiving distributions</li>
                        <li>Accurate personal information</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">4</span>
                    Payment Terms
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>All payments on the Platform are processed in USDT (Tether) cryptocurrency:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li><strong class="text-white">Deposits:</strong> USDT via TRC20 (recommended), BEP20, or ERC20 networks</li>
                        <li><strong class="text-white">Withdrawals:</strong> Sent to your registered USDT wallet address</li>
                        <li><strong class="text-white">Network Fees:</strong> You are responsible for blockchain network fees</li>
                        <li><strong class="text-white">Conversion:</strong> Values displayed in ZAR for reference; actual payments in USDT</li>
                    </ul>
                </div>
            </section>

            <!-- Section 5 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">5</span>
                    Risk Disclosure
                </h2>
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                    <p class="text-red-300 leading-relaxed">
                        <strong>Important:</strong> All partnership activities carry inherent risk. Past performance does not guarantee future results. You may lose some or all of your contribution. Only contribute funds you can afford to lose. The Platform does not provide financial advice.
                    </p>
                </div>
            </section>

            <!-- Section 6 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">6</span>
                    User Responsibilities
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>As a user of the Platform, you agree to:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li>Provide accurate and truthful information</li>
                        <li>Maintain the security of your account credentials</li>
                        <li>Verify your wallet address before submitting (incorrect addresses may result in loss of funds)</li>
                        <li>Comply with all applicable laws and regulations</li>
                        <li>Not use the Platform for money laundering or illegal activities</li>
                    </ul>
                </div>
            </section>

            <!-- Section 7 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">7</span>
                    Governance & Voting
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    Partners have voting rights on cohort decisions proportional to their contribution. Voting is conducted through the Platform's voting system. Majority decisions are binding on all cohort members.
                </p>
            </section>

            <!-- Section 8 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">8</span>
                    Limitation of Liability
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    The Platform and its operators shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the service, including but not limited to loss of profits, data, or other intangible losses.
                </p>
            </section>

            <!-- Section 9 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">9</span>
                    Modifications
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    We reserve the right to modify these terms at any time. Users will be notified of significant changes via email or platform notification. Continued use of the Platform after changes constitutes acceptance of modified terms.
                </p>
            </section>

            <!-- Section 10 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 text-amber-400 text-sm">10</span>
                    Contact Information
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    For questions regarding these Terms & Conditions, please contact us at:
                    <br><br>
                    <strong class="text-white">Email:</strong> legal@roundtable.co.za<br>
                    <strong class="text-white">Address:</strong> Johannesburg, South Africa
                </p>
            </section>

        </div>

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl transition">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
