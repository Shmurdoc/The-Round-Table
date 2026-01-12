@extends('layouts.modern')

@section('title', 'Create Cohort - RoundTable')
@section('page-title', 'Create Cohort')

@section('content')
<div class="slide-up space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-600"></i>
                </div>
                Create New Cohort
            </h1>
            <p class="text-slate-500 text-sm mt-2">Set up a new investment cohort for members to join</p>
        </div>
        <a href="{{ route('admin.cohorts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back
        </a>
    </div>

    <!-- Error Display -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-800">Please correct the following errors:</h4>
                    <ul class="mt-2 space-y-1 text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.cohorts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                    Basic Information
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Cohort Name <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-red-500 @enderror"
                        placeholder="e.g., Tech Growth Fund 2026">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-red-500 @enderror"
                        placeholder="Describe the investment thesis and goals...">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cohort Class <span class="text-red-500">*</span></label>
                        <select name="cohort_class" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('cohort_class') border-red-500 @enderror">
                            <option value="">Select cohort class...</option>
                            <option value="utilization" {{ old('cohort_class') == 'utilization' ? 'selected' : '' }}>Utilization (Rental Income)</option>
                            <option value="lease" {{ old('cohort_class') == 'lease' ? 'selected' : '' }}>Lease (Asset Leasing)</option>
                            <option value="resale" {{ old('cohort_class') == 'resale' ? 'selected' : '' }}>Resale (Buy & Sell)</option>
                            <option value="hybrid" {{ old('cohort_class') == 'hybrid' ? 'selected' : '' }}>Hybrid (Mixed Strategy)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Asset Type <span class="text-red-500">*</span></label>
                        <select name="asset_type" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('asset_type') border-red-500 @enderror">
                            <option value="">Select asset type...</option>
                            <option value="real_estate" {{ old('asset_type') == 'real_estate' ? 'selected' : '' }}>Real Estate</option>
                            <option value="equipment" {{ old('asset_type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="business" {{ old('asset_type') == 'business' ? 'selected' : '' }}>Business</option>
                            <option value="renewable_energy" {{ old('asset_type') == 'renewable_energy' ? 'selected' : '' }}>Renewable Energy</option>
                            <option value="intellectual_property" {{ old('asset_type') == 'intellectual_property' ? 'selected' : '' }}>Intellectual Property</option>
                            <option value="other" {{ old('asset_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Image & Gallery -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-slate-500"></i>
                    Featured Image & Gallery
                </h3>
                <p class="text-xs text-slate-500 mt-1">Add a featured image and optional gallery images for this cohort</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- Featured Image -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Featured Image <span class="text-amber-600">(Recommended)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-amber-400 transition-colors bg-slate-50/50 hover:bg-amber-50/30">
                        <input type="file" 
                               name="featured_image" 
                               id="featured_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewFeaturedImage(event)">
                        <label for="featured_image" class="cursor-pointer">
                            <div id="featured_image_preview" class="hidden mb-4">
                                <img src="" alt="Preview" class="mx-auto max-h-48 rounded-xl shadow-lg">
                            </div>
                            <div id="featured_image_placeholder">
                                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-amber-600"></i>
                                </div>
                                <p class="text-slate-700 font-medium mb-1">Click to upload featured image</p>
                                <p class="text-sm text-slate-500">PNG, JPG, WEBP up to 5MB (recommended: 1200x600px)</p>
                            </div>
                        </label>
                    </div>
                    @error('featured_image')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Additional Images Gallery -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Additional Images <span class="text-slate-500">(Optional)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-amber-400 transition-colors bg-slate-50/50 hover:bg-amber-50/30">
                        <input type="file" 
                               name="images[]" 
                               id="gallery_images"
                               accept="image/*"
                               multiple
                               class="hidden"
                               onchange="previewGalleryImages(event)">
                        <label for="gallery_images" class="cursor-pointer">
                            <div id="gallery_preview" class="hidden mb-4 grid grid-cols-4 gap-3"></div>
                            <div id="gallery_placeholder">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="images" class="w-8 h-8 text-slate-600"></i>
                                </div>
                                <p class="text-slate-700 font-medium mb-1">Click to upload gallery images</p>
                                <p class="text-sm text-slate-500">Multiple images allowed (PNG, JPG, WEBP)</p>
                            </div>
                        </label>
                    </div>
                    @error('images')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Capital Requirements -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="banknote" class="w-4 h-4 text-slate-500"></i>
                    Capital Requirements
                </h3>
                <p class="text-xs text-slate-500 mt-1">All amounts in cents. Minimum R3,000 = 300000 cents.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Viable Capital <span class="text-red-500">*</span></label>
                        <input type="number" name="minimum_viable_capital" value="{{ old('minimum_viable_capital', 300000) }}" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('minimum_viable_capital') border-red-500 @enderror"
                            placeholder="300000">
                        <p class="text-xs text-slate-400 mt-1">Min to proceed (300000 = R3,000)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Ideal Target <span class="text-red-500">*</span></label>
                        <input type="number" name="ideal_target" value="{{ old('ideal_target', 1000000) }}" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('ideal_target') border-red-500 @enderror"
                            placeholder="1000000">
                        <p class="text-xs text-slate-400 mt-1">Optimal funding goal</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hard Cap <span class="text-red-500">*</span></label>
                        <input type="number" name="hard_cap" value="{{ old('hard_cap', 2000000) }}" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('hard_cap') border-red-500 @enderror"
                            placeholder="2000000">
                        <p class="text-xs text-slate-400 mt-1">Max funding limit</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Min Contribution <span class="text-red-500">*</span></label>
                        <input type="number" name="min_contribution" value="{{ old('min_contribution', 300000) }}" min="300000" max="10000000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('min_contribution') border-red-500 @enderror"
                            placeholder="300000">
                        <p class="text-xs text-slate-400 mt-1">Min per member (R3k-R100k)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Max Contribution <span class="text-red-500">*</span></label>
                        <input type="number" name="max_contribution" value="{{ old('max_contribution', 10000000) }}" min="300000" max="10000000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('max_contribution') border-red-500 @enderror"
                            placeholder="10000000">
                        <p class="text-xs text-slate-400 mt-1">Max per member (R3k-R100k)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                    Timeline & Duration
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Funding Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="funding_start_date" value="{{ old('funding_start_date') }}" required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('funding_start_date') border-red-500 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Funding End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="funding_end_date" value="{{ old('funding_end_date') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('funding_end_date') border-red-500 @enderror">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Expected Exit Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expected_exit_date" value="{{ old('expected_exit_date') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('expected_exit_date') border-red-500 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_months" value="{{ old('duration_months', 12) }}" min="1" max="120" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('duration_months') border-red-500 @enderror"
                            placeholder="12">
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk & Returns -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4 text-slate-500"></i>
                    Risk & Exit Strategy
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Risk Level <span class="text-red-500">*</span></label>
                        <select name="risk_level" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('risk_level') border-red-500 @enderror">
                            <option value="">Select risk level...</option>
                            <option value="low" {{ old('risk_level') == 'low' ? 'selected' : '' }}>Low Risk</option>
                            <option value="moderate" {{ old('risk_level') == 'moderate' ? 'selected' : '' }}>Moderate Risk</option>
                            <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>High Risk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months', 12) }}" min="1" max="120" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('duration_months') border-red-500 @enderror"
                            placeholder="12">
                        <p class="text-xs text-slate-400 mt-1">Project duration (1-120 months)</p>
                    </div>
                </div>

                <!-- Return Calculation Section -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl p-5 space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                        <h4 class="font-bold text-emerald-900">Return Projections</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Expected Total Payout (ZAR)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">R</span>
                                <input type="number" name="expected_total_payout" id="expected_total_payout" value="{{ old('expected_total_payout') }}" min="0" step="0.01"
                                    class="w-full pl-9 pr-4 py-3 bg-white border-2 border-emerald-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    placeholder="50000.00">
                            </div>
                            <p class="text-xs text-emerald-700 mt-1.5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i>
                                Total expected returns at project end
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Projected Annual Return</label>
                            <div class="relative">
                                <input type="number" name="projected_annual_return" id="projected_annual_return" value="{{ old('projected_annual_return') }}" min="0" max="1000" step="0.01" readonly
                                    class="w-full px-4 py-3 bg-white border-2 border-emerald-300 rounded-xl text-sm font-bold text-emerald-700 focus:outline-none cursor-not-allowed"
                                    placeholder="0.00">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-600 font-bold text-xl">%</span>
                            </div>
                            <p class="text-xs text-emerald-700 mt-1.5 flex items-center gap-1">
                                <i data-lucide="calculator" class="w-3 h-3"></i>
                                Auto-calculated based on capital & payout
                            </p>
                        </div>
                    </div>

                    <!-- Calculation Display -->
                    <div id="calculation_display" class="bg-white/70 rounded-lg p-4 border border-emerald-200 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <div class="text-slate-500 text-xs mb-1">Investment Capital</div>
                                <div class="font-bold text-slate-900" id="display_capital">R 0</div>
                            </div>
                            <div>
                                <div class="text-slate-500 text-xs mb-1">Total Returns</div>
                                <div class="font-bold text-emerald-700" id="display_returns">R 0</div>
                            </div>
                            <div>
                                <div class="text-slate-500 text-xs mb-1">Net Profit</div>
                                <div class="font-bold text-emerald-700" id="display_profit">R 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-blue-800 flex items-start gap-2">
                            <i data-lucide="lightbulb" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
                            <span><strong>How it works:</strong> Enter the expected total payout. The system calculates the annual return % based on your ideal target capital and project duration.</span>
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Exit Strategy <span class="text-red-500">*</span></label>
                    <textarea name="exit_strategy" rows="3" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('exit_strategy') border-red-500 @enderror"
                        placeholder="Describe how investors will exit and receive returns...">{{ old('exit_strategy') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Risk Factors (Optional)</label>
                    <textarea name="risk_factors" rows="2"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                        placeholder="Describe potential risks...">{{ old('risk_factors') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex gap-4 justify-end">
            <a href="{{ route('admin.cohorts.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-200">
                <i data-lucide="check" class="w-5 h-5"></i>
                Create Cohort
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    // Image Preview Functions
    function previewFeaturedImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('featured_image_preview').classList.remove('hidden');
                document.getElementById('featured_image_preview').querySelector('img').src = e.target.result;
                document.getElementById('featured_image_placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function previewGalleryImages(event) {
        const files = event.target.files;
        if (files.length > 0) {
            const gallery = document.getElementById('gallery_preview');
            gallery.innerHTML = '';
            gallery.classList.remove('hidden');
            document.getElementById('gallery_placeholder').classList.add('hidden');
            
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Gallery ${index + 1}" 
                             class="w-full h-24 object-cover rounded-lg border-2 border-slate-200 group-hover:border-amber-400 transition-colors">
                        <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                        </div>
                    `;
                    gallery.appendChild(div);
                    lucide.createIcons();
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // Auto-calculate Projected Annual Return
    function calculateAnnualReturn() {
        const idealTarget = parseFloat(document.querySelector('input[name="ideal_target"]').value) || 0;
        const expectedPayout = parseFloat(document.getElementById('expected_total_payout').value) || 0;
        const durationMonths = parseFloat(document.getElementById('duration_months').value) || 12;
        
        if (idealTarget > 0 && expectedPayout > 0 && durationMonths > 0) {
            // Convert cents to rands for display
            const capitalInRands = idealTarget / 100;
            const payoutInRands = expectedPayout;
            
            // Calculate profit
            const profit = payoutInRands - capitalInRands;
            
            // Calculate total return percentage
            const totalReturnPercent = (profit / capitalInRands) * 100;
            
            // Annualize the return
            const yearsInProject = durationMonths / 12;
            const annualReturn = totalReturnPercent / yearsInProject;
            
            // Update the field
            document.getElementById('projected_annual_return').value = annualReturn.toFixed(2);
            
            // Update display
            document.getElementById('display_capital').textContent = 'R ' + capitalInRands.toLocaleString('en-ZA', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('display_returns').textContent = 'R ' + payoutInRands.toLocaleString('en-ZA', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('display_profit').textContent = 'R ' + profit.toLocaleString('en-ZA', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('calculation_display').classList.remove('hidden');
        } else {
            document.getElementById('projected_annual_return').value = '';
            document.getElementById('calculation_display').classList.add('hidden');
        }
    }

    // Attach listeners
    document.querySelector('input[name="ideal_target"]').addEventListener('input', calculateAnnualReturn);
    document.getElementById('expected_total_payout').addEventListener('input', calculateAnnualReturn);
    document.getElementById('duration_months').addEventListener('input', calculateAnnualReturn);
    
    // Calculate on page load if values exist
    document.addEventListener('DOMContentLoaded', calculateAnnualReturn);
</script>
@endpush
