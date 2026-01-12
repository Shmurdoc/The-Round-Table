<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ImpactSimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpactSimulatorController extends Controller
{
    public function __construct(
        private ImpactSimulatorService $simulatorService
    ) {}

    /**
     * Show simulator page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('simulator.index', [
            'user' => $user,
            'cohortTypes' => [
                'utilization' => 'Utilization Cohort',
                'lease' => 'Lease Cohort',
                'resale' => 'Resale (Flip) Cohort',
                'hybrid' => 'Hybrid Cohort',
            ],
        ]);
    }

    /**
     * Run utilization cohort simulation
     */
    public function simulateUtilization(Request $request)
    {
        $request->validate([
            'acquisition_cost' => 'required|numeric|min:0',
            'improvement_budget' => 'nullable|numeric|min:0',
            'daily_rate' => 'required|numeric|min:0',
            'utilization_rate' => 'required|numeric|min:0|max:1',
            'expense_ratio' => 'nullable|numeric|min:0|max:1',
            'admin_fee_percent' => 'nullable|numeric|min:0|max:0.5',
            'duration_months' => 'required|integer|min:1',
            'member_contribution' => 'required|numeric|min:3000|max:100000',
            'total_capital' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $params = $request->all();
        $params['member_tier'] = $user->tier ?? 1;
        $params['admin_experience'] = $request->input('admin_experience', 5);

        $result = $this->simulatorService->simulateUtilizationCohort($params);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Run lease cohort simulation
     */
    public function simulateLease(Request $request)
    {
        $request->validate([
            'acquisition_cost' => 'required|numeric|min:0',
            'renovation_budget' => 'nullable|numeric|min:0',
            'monthly_lease' => 'required|numeric|min:0',
            'vacancy_rate' => 'nullable|numeric|min:0|max:1',
            'property_mgmt_fee' => 'nullable|numeric|min:0|max:1',
            'maintenance_reserve' => 'nullable|numeric|min:0',
            'annual_tax_insurance' => 'nullable|numeric|min:0',
            'admin_fee_percent' => 'nullable|numeric|min:0|max:0.5',
            'holding_period_years' => 'required|numeric|min:0.5',
            'exit_cap_rate' => 'nullable|numeric|min:0|max:1',
            'member_contribution' => 'required|numeric|min:3000|max:100000',
            'total_capital' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $params = $request->all();
        $params['member_tier'] = $user->tier ?? 1;

        $result = $this->simulatorService->simulateLeaseCohort($params);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Run resale cohort simulation
     */
    public function simulateResale(Request $request)
    {
        $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'acquisition_costs' => 'nullable|numeric|min:0',
            'renovation_budget' => 'required|numeric|min:0',
            'holding_costs' => 'nullable|numeric|min:0',
            'holding_months' => 'required|integer|min:1',
            'target_resale_price' => 'required|numeric|min:0',
            'admin_setup_fee' => 'nullable|numeric|min:0',
            'admin_exit_fee' => 'nullable|numeric|min:0',
            'member_contribution' => 'required|numeric|min:3000|max:100000',
            'total_capital' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $params = $request->all();
        $params['member_tier'] = $user->tier ?? 1;

        $result = $this->simulatorService->simulateResaleCohort($params);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Run Monte Carlo simulation
     */
    public function monteCarlo(Request $request)
    {
        $request->validate([
            'cohort_type' => 'required|in:utilization,lease,resale',
            'iterations' => 'nullable|integer|min:1000|max:50000',
        ]);

        $user = Auth::user();
        $params = $request->except(['cohort_type', 'iterations']);
        $params['member_tier'] = $user->tier ?? 1;

        $iterations = $request->input('iterations', 10000);
        $cohortType = $request->input('cohort_type');

        // This may take a few seconds
        $result = $this->simulatorService->runMonteCarloSimulation(
            $params,
            $cohortType,
            $iterations
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Analyze portfolio diversification
     */
    public function analyzePortfolio(Request $request)
    {
        $user = Auth::user();
        
        $plannedCohorts = $request->input('planned_cohorts', []);

        $result = $this->simulatorService->analyzePortfolio($user, $plannedCohorts);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Save simulation for later
     */
    public function saveSimulation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cohort_type' => 'required|string',
            'parameters' => 'required|array',
            'results' => 'required|array',
        ]);

        $user = Auth::user();

        $simulation = $user->savedSimulations()->create([
            'name' => $request->name,
            'cohort_type' => $request->cohort_type,
            'parameters' => $request->parameters,
            'results' => $request->results,
        ]);

        return response()->json([
            'success' => true,
            'simulation_id' => $simulation->id,
            'message' => 'Simulation saved successfully',
        ]);
    }

    /**
     * Export simulation results as PDF
     */
    public function exportPDF(Request $request)
    {
        $request->validate([
            'simulation_data' => 'required|array',
        ]);

        // Generate PDF (use package like DomPDF or similar)
        // For now, return JSON indicating export would happen
        
        return response()->json([
            'success' => true,
            'message' => 'PDF export initiated',
            'download_url' => '/downloads/simulation_' . time() . '.pdf',
        ]);
    }

    /**
     * Compare multiple scenarios side-by-side
     */
    public function compareScenarios(Request $request)
    {
        $request->validate([
            'scenarios' => 'required|array|min:2|max:5',
        ]);

        $user = Auth::user();
        $scenarios = $request->input('scenarios');
        $results = [];

        foreach ($scenarios as $scenario) {
            $params = $scenario['parameters'];
            $params['member_tier'] = $user->tier ?? 1;

            $result = match($scenario['type']) {
                'utilization' => $this->simulatorService->simulateUtilizationCohort($params),
                'lease' => $this->simulatorService->simulateLeaseCohort($params),
                'resale' => $this->simulatorService->simulateResaleCohort($params),
                default => null,
            };

            if ($result) {
                $results[] = [
                    'name' => $scenario['name'] ?? 'Scenario ' . (count($results) + 1),
                    'data' => $result,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'comparison' => $results,
        ]);
    }
}
