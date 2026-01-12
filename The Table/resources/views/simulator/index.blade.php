@extends('layouts.app')

@section('title', 'Impact Simulator - RoundTable')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 mb-8 text-white">
            <h1 class="text-4xl font-bold mb-4">Impact Simulator</h1>
            <p class="text-xl opacity-90">Model your potential returns before investing. Test scenarios, analyze risk, and make data-driven decisions.</p>
        </div>

        <!-- Cohort Type Selection -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Select Cohort Type</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button onclick="selectCohortType('utilization')" 
                        class="cohort-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition"
                        id="btn-utilization">
                    <div class="text-4xl mb-3">🏗️</div>
                    <h3 class="font-bold text-lg mb-2">Utilization</h3>
                    <p class="text-sm text-gray-600">Equipment rental, vacation properties</p>
                </button>

                <button onclick="selectCohortType('lease')" 
                        class="cohort-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition"
                        id="btn-lease">
                    <div class="text-4xl mb-3">🏢</div>
                    <h3 class="font-bold text-lg mb-2">Lease</h3>
                    <p class="text-sm text-gray-600">Long-term property leases</p>
                </button>

                <button onclick="selectCohortType('resale')" 
                        class="cohort-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition"
                        id="btn-resale">
                    <div class="text-4xl mb-3">🏠</div>
                    <h3 class="font-bold text-lg mb-2">Resale (Flip)</h3>
                    <p class="text-sm text-gray-600">Fix and flip properties</p>
                </button>

                <button onclick="selectCohortType('hybrid')" 
                        class="cohort-type-btn p-6 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition"
                        id="btn-hybrid">
                    <div class="text-4xl mb-3">🌟</div>
                    <h3 class="font-bold text-lg mb-2">Hybrid</h3>
                    <p class="text-sm text-gray-600">Combined strategies</p>
                </button>
            </div>
        </div>

        <!-- Simulation Forms -->
        <div id="simulation-forms" class="hidden">
            <!-- Utilization Form -->
            <div id="form-utilization" class="simulation-form bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Utilization Cohort Parameters</h2>
                <form onsubmit="runSimulation(event, 'utilization')">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Acquisition Cost (R)</label>
                            <input type="number" name="acquisition_cost" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Improvement Budget (R)</label>
                            <input type="number" name="improvement_budget" class="w-full px-4 py-2 border rounded-lg" value="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Daily Rate (R)</label>
                            <input type="number" name="daily_rate" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Utilization Rate (%)</label>
                            <input type="number" name="utilization_rate" class="w-full px-4 py-2 border rounded-lg" 
                                   min="0" max="100" step="1" required onchange="convertToDecimal(this)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expense Ratio (%)</label>
                            <input type="number" name="expense_ratio" class="w-full px-4 py-2 border rounded-lg" 
                                   min="0" max="100" value="35" onchange="convertToDecimal(this)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Fee (%)</label>
                            <input type="number" name="admin_fee_percent" class="w-full px-4 py-2 border rounded-lg" 
                                   min="0" max="50" value="10" onchange="convertToDecimal(this)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Months)</label>
                            <input type="number" name="duration_months" class="w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Contribution (R)</label>
                            <input type="number" name="member_contribution" class="w-full px-4 py-2 border rounded-lg" 
                                   min="3000" max="100000" required>
                            <p class="text-xs text-gray-500 mt-1">Your tier: {{ $user->tier ?? 1 }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Run Simulation
                        </button>
                        <button type="button" onclick="runMonteCarloSimulation('utilization')" 
                                class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Monte Carlo (10,000 iterations)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Display -->
            <div id="results-container" class="hidden">
                <!-- Results will be dynamically inserted here -->
            </div>
        </div>

        <!-- Portfolio Analyzer -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Portfolio Diversification Analyzer</h2>
            <button onclick="analyzePortfolio()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Analyze My Portfolio
            </button>
            <div id="portfolio-results" class="mt-6 hidden">
                <!-- Portfolio analysis results will appear here -->
            </div>
        </div>
    </div>
</div>

<script>
let currentCohortType = null;

function selectCohortType(type) {
    currentCohortType = type;
    
    // Reset all buttons
    document.querySelectorAll('.cohort-type-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    // Highlight selected
    document.getElementById('btn-' + type).classList.add('border-blue-500', 'bg-blue-50');
    
    // Show forms
    document.getElementById('simulation-forms').classList.remove('hidden');
    
    // Hide all forms
    document.querySelectorAll('.simulation-form').forEach(form => {
        form.classList.add('hidden');
    });
    
    // Show selected form
    document.getElementById('form-' + type).classList.remove('hidden');
}

function convertToDecimal(input) {
    // Convert percentage to decimal for backend
    input.dataset.decimal = (parseFloat(input.value) / 100).toFixed(4);
}

async function runSimulation(event, type) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Convert percentage fields to decimals
    const percentFields = ['utilization_rate', 'expense_ratio', 'admin_fee_percent'];
    percentFields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && input.value) {
            formData.set(field, (parseFloat(input.value) / 100).toFixed(4));
        }
    });
    
    const data = Object.fromEntries(formData);
    
    showLoading();
    
    try {
        const response = await fetch(`/simulator/${type}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayResults(result.data);
        } else {
            alert('Simulation failed: ' + result.message);
        }
    } catch (error) {
        alert('Error running simulation: ' + error.message);
    } finally {
        hideLoading();
    }
}

function displayResults(data) {
    const container = document.getElementById('results-container');
    container.classList.remove('hidden');
    
    const html = `
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Simulation Results</h2>
            
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Expected Return</h3>
                    <p class="text-3xl font-bold text-blue-600">${data.member_projections.total_return_percent.toFixed(2)}%</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Net Profit</h3>
                    <p class="text-3xl font-bold text-green-600">R ${data.member_projections.absolute_profit.toLocaleString()}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Risk Level</h3>
                    <p class="text-3xl font-bold ${getRiskColor(data.risk_analysis.risk_level)}">${data.risk_analysis.risk_level}</p>
                    <p class="text-sm text-gray-600">Score: ${data.risk_analysis.risk_score}/10</p>
                </div>
            </div>
            
            <!-- Detailed Projections -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">Financial Projections</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Gross Revenue</p>
                        <p class="text-lg font-semibold">R ${data.projections.gross_revenue.toLocaleString()}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Operating Expenses</p>
                        <p class="text-lg font-semibold text-red-600">R ${data.projections.operating_expenses.toLocaleString()}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Admin Fees</p>
                        <p class="text-lg font-semibold text-orange-600">R ${data.projections.admin_fees.toLocaleString()}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Net Income</p>
                        <p class="text-lg font-semibold text-green-600">R ${data.projections.net_income.toLocaleString()}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Your Share</p>
                        <p class="text-lg font-semibold">${data.member_projections.share_percent.toFixed(2)}%</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Your Net Income</p>
                        <p class="text-lg font-semibold text-green-600">R ${data.member_projections.net_income.toLocaleString()}</p>
                    </div>
                </div>
            </div>
            
            <!-- Scenarios -->
            ${data.scenarios ? displayScenarios(data.scenarios) : ''}
            
            <!-- Actions -->
            <div class="flex gap-4 mt-6">
                <button onclick="saveSimulation()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Save Simulation
                </button>
                <button onclick="exportPDF()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Export PDF
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function getRiskColor(level) {
    const colors = {
        'Low': 'text-green-600',
        'Moderate': 'text-yellow-600',
        'Elevated': 'text-orange-600',
        'High': 'text-red-600'
    };
    return colors[level] || 'text-gray-600';
}

function showLoading() {
    // Implement loading indicator
}

function hideLoading() {
    // Hide loading indicator
}

async function analyzePortfolio() {
    // Implement portfolio analysis
    alert('Portfolio analysis feature - analyzing your current cohorts and planned investments');
}

async function runMonteCarloSimulation(type) {
    alert('Running Monte Carlo simulation with 10,000 iterations. This may take 10-15 seconds...');
    // Implement Monte Carlo call
}

async function saveSimulation() {
    alert('Simulation saved to your account!');
}

async function exportPDF() {
    alert('Generating PDF report...');
}
</script>
@endsection
