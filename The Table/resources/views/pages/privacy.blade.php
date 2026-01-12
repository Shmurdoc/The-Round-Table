@extends('layouts.modern')

@section('title', 'Privacy Policy')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Privacy Policy</h1>
            <p class="text-slate-400">Last updated: January 9, 2026</p>
        </div>

        <!-- Content Card -->
        <div class="bg-slate-800/50 backdrop-blur-xl rounded-3xl border border-slate-700/50 p-8 md:p-12 space-y-8">
            
            <!-- Introduction -->
            <section>
                <p class="text-slate-300 leading-relaxed">
                    RoundTable Partnership Platform ("we", "our", or "Platform") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Platform.
                </p>
            </section>

            <!-- Section 1 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">1</span>
                    Information We Collect
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Personal Information</h3>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Full name, email address, phone number</li>
                            <li>South African ID number and document copies</li>
                            <li>Proof of residence documents</li>
                            <li>Physical address</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Financial Information</h3>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>USDT wallet addresses (TRC20, BEP20, ERC20)</li>
                            <li>Transaction history and amounts</li>
                            <li>Partnership contributions and distributions</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Technical Information</h3>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>IP address and device information</li>
                            <li>Browser type and version</li>
                            <li>Login timestamps and session data</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">2</span>
                    How We Use Your Information
                </h2>
                <div class="text-slate-300 leading-relaxed">
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li><strong class="text-white">Identity Verification:</strong> To comply with KYC/AML regulations</li>
                        <li><strong class="text-white">Service Provision:</strong> To process contributions, distributions, and withdrawals</li>
                        <li><strong class="text-white">Communication:</strong> To send notifications about your account and partnerships</li>
                        <li><strong class="text-white">Security:</strong> To detect and prevent fraud and unauthorized access</li>
                        <li><strong class="text-white">Legal Compliance:</strong> To meet regulatory requirements</li>
                        <li><strong class="text-white">Improvement:</strong> To enhance our Platform and user experience</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">3</span>
                    Data Protection (POPIA Compliance)
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>We comply with the Protection of Personal Information Act (POPIA) of South Africa:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li>Data is processed lawfully and in a reasonable manner</li>
                        <li>Collection is limited to what is necessary for our purposes</li>
                        <li>Information is kept accurate and up to date</li>
                        <li>Data is stored securely with appropriate safeguards</li>
                        <li>Personal information is not kept longer than necessary</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">4</span>
                    Data Security
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>We implement industry-standard security measures:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li>SSL/TLS encryption for all data transmission</li>
                        <li>Encrypted storage of sensitive documents</li>
                        <li>Regular security audits and updates</li>
                        <li>Access controls and authentication</li>
                        <li>Activity logging and monitoring</li>
                    </ul>
                </div>
            </section>

            <!-- Section 5 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">5</span>
                    Information Sharing
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>We do not sell your personal information. We may share data with:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li><strong class="text-white">Payment Processors:</strong> NOWPayments for USDT transactions</li>
                        <li><strong class="text-white">Legal Authorities:</strong> When required by law or court order</li>
                        <li><strong class="text-white">Cohort Administrators:</strong> Limited information for partnership management</li>
                        <li><strong class="text-white">Service Providers:</strong> Who assist in operating the Platform (under strict confidentiality)</li>
                    </ul>
                </div>
            </section>

            <!-- Section 6 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">6</span>
                    Your Rights
                </h2>
                <div class="text-slate-300 leading-relaxed space-y-3">
                    <p>Under POPIA, you have the right to:</p>
                    <ul class="list-disc list-inside ml-4 space-y-2">
                        <li>Access your personal information</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your data (subject to legal requirements)</li>
                        <li>Object to processing of your information</li>
                        <li>Withdraw consent (where applicable)</li>
                        <li>Lodge a complaint with the Information Regulator</li>
                    </ul>
                </div>
            </section>

            <!-- Section 7 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">7</span>
                    Data Retention
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    We retain your personal information for as long as your account is active and for 5 years thereafter to comply with legal and regulatory requirements. Financial transaction records are kept for 7 years as required by South African law.
                </p>
            </section>

            <!-- Section 8 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">8</span>
                    Cookies & Tracking
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    We use essential cookies to maintain your session and preferences. We do not use third-party tracking cookies for advertising purposes. You can manage cookie preferences in your browser settings.
                </p>
            </section>

            <!-- Section 9 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">9</span>
                    Changes to This Policy
                </h2>
                <p class="text-slate-300 leading-relaxed">
                    We may update this Privacy Policy periodically. We will notify you of significant changes via email or platform notification. The date at the top of this policy indicates when it was last updated.
                </p>
            </section>

            <!-- Section 10 -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-3 text-emerald-400 text-sm">10</span>
                    Contact Us
                </h2>
                <div class="text-slate-300 leading-relaxed">
                    <p>For privacy-related inquiries or to exercise your rights:</p>
                    <br>
                    <p>
                        <strong class="text-white">Information Officer:</strong> Privacy Officer<br>
                        <strong class="text-white">Email:</strong> privacy@roundtable.co.za<br>
                        <strong class="text-white">Address:</strong> Johannesburg, South Africa
                    </p>
                </div>
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
