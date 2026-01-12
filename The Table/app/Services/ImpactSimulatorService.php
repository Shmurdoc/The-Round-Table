<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ImpactSimulatorService
{
    /**
     * Run utilization cohort simulation
     */
    public function simulateUtilizationCohort(array $params): array
    {
        $acquisitionCost = $params['acquisition_cost'];
        $improvementBudget = $params['improvement_budget'] ?? 0;
        $dailyRate = $params['daily_rate'];
        $utilizationRate = $params['utilization_rate']; // 0-1
        $expenseRatio = $params['expense_ratio'] ?? 0.35; // % of revenue
        $adminFeePercent = $params['admin_fee_percent'] ?? 0.10; // 10%
        $durationMonths = $params['duration_months'];
        $memberContribution = $params['member_contribution'];
        $memberTier = $params['member_tier'] ?? 1;
        $totalCapital = $params['total_capital'] ?? $acquisitionCost + $improvementBudget;

        // Calculate projections
        $daysPerMonth = 30;
        $totalDays = $durationMonths * $daysPerMonth;
        $utilizedDays = $totalDays * $utilizationRate;

        $grossRevenue = $dailyRate * $utilizedDays;
        $operatingExpenses = $grossRevenue * $expenseRatio;
        $adminFees = $grossRevenue * $adminFeePercent;
        $netIncome = $grossRevenue - $operatingExpenses - $adminFees;

        // Residual value (assume 80% of acquisition cost)
        $residualValue = $acquisitionCost * 0.80;
        
        // Total return
        $totalInvestment = $acquisitionCost + $improvementBudget;
        $totalReturn = (($netIncome + $residualValue - $totalInvestment) / $totalInvestment) * 100;
        $annualizedReturn = $totalReturn / ($durationMonths / 12);

        // Member-specific calculations
        $memberSharePercent = $memberContribution / $totalCapital;
        $memberNetIncome = $netIncome * $memberSharePercent;
        $memberResidual = $residualValue * $memberSharePercent;
        $memberTotalReturn = (($memberNetIncome + $memberResidual - $memberContribution) / $memberContribution) * 100;

        // Apply tier benefits (fee discount)
        $tierFeeDiscount = $this->getTierFeeDiscount($memberTier);
        $memberNetIncomeAdjusted = $memberNetIncome * (1 + $tierFeeDiscount);

        // Risk score calculation
        $riskScore = $this->calculateRiskScore([
            'utilization_rate' => $utilizationRate,
            'expense_ratio' => $expenseRatio,
            'admin_experience' => $params['admin_experience'] ?? 5,
        ]);

        // Scenario analysis
        $scenarios = $this->runScenarios($params, 'utilization');

        return [
            'cohort_type' => 'utilization',
            'projections' => [
                'gross_revenue' => round($grossRevenue, 2),
                'operating_expenses' => round($operatingExpenses, 2),
                'admin_fees' => round($adminFees, 2),
                'net_income' => round($netIncome, 2),
                'residual_value' => round($residualValue, 2),
                'total_return_percent' => round($totalReturn, 2),
                'annualized_return_percent' => round($annualizedReturn, 2),
            ],
            'member_projections' => [
                'share_percent' => round($memberSharePercent * 100, 2),
                'net_income' => round($memberNetIncome, 2),
                'net_income_with_tier_benefits' => round($memberNetIncomeAdjusted, 2),
                'residual_share' => round($memberResidual, 2),
                'total_return_percent' => round($memberTotalReturn, 2),
                'absolute_profit' => round($memberNetIncome + $memberResidual - $memberContribution, 2),
            ],
            'risk_analysis' => [
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
                'key_risks' => $this->identifyKeyRisks($params, 'utilization'),
            ],
            'scenarios' => $scenarios,
            'sensitivity_factors' => $this->calculateSensitivity($params, 'utilization'),
        ];
    }

    /**
     * Run lease cohort simulation
     */
    public function simulateLeaseCohort(array $params): array
    {
        $acquisitionCost = $params['acquisition_cost'];
        $renovationBudget = $params['renovation_budget'] ?? 0;
        $monthlyLease = $params['monthly_lease'];
        $vacancyRate = $params['vacancy_rate'] ?? 0.05; // 5%
        $propertyMgmtFee = $params['property_mgmt_fee'] ?? 0.08; // 8%
        $maintenanceReserve = $params['maintenance_reserve'] ?? 1000; // per month
        $annualTaxInsurance = $params['annual_tax_insurance'] ?? 12000;
        $adminFeePercent = $params['admin_fee_percent'] ?? 0.10;
        $holdingPeriodYears = $params['holding_period_years'];
        $exitCapRate = $params['exit_cap_rate'] ?? 0.07; // 7%
        $memberContribution = $params['member_contribution'];
        $memberTier = $params['member_tier'] ?? 1;
        $totalCapital = $params['total_capital'] ?? $acquisitionCost + $renovationBudget;

        // Annual calculations
        $annualGrossRent = $monthlyLease * 12 * (1 - $vacancyRate);
        $annualPropertyMgmt = $annualGrossRent * $propertyMgmtFee;
        $annualMaintenance = $maintenanceReserve * 12;
        $annualOperatingCosts = $annualPropertyMgmt + $annualMaintenance + $annualTaxInsurance;
        $annualAdminFees = $annualGrossRent * $adminFeePercent;
        $annualNOI = $annualGrossRent - $annualOperatingCosts - $annualAdminFees;
        
        // Total period calculations
        $totalNOI = $annualNOI * $holdingPeriodYears;
        
        // Exit value estimation
        $exitValue = $annualNOI / $exitCapRate;
        
        // Total return
        $totalInvestment = $acquisitionCost + $renovationBudget;
        $totalProceeds = $exitValue + $totalNOI;
        $totalReturn = (($totalProceeds - $totalInvestment) / $totalInvestment) * 100;
        $annualizedReturn = $totalReturn / $holdingPeriodYears;

        // Member-specific
        $memberSharePercent = $memberContribution / $totalCapital;
        $memberNOI = $totalNOI * $memberSharePercent;
        $memberExitValue = $exitValue * $memberSharePercent;
        $memberTotalReturn = (($memberNOI + $memberExitValue - $memberContribution) / $memberContribution) * 100;

        // Risk score
        $riskScore = $this->calculateRiskScore([
            'vacancy_rate' => $vacancyRate,
            'market_stability' => $params['market_stability'] ?? 7,
            'property_condition' => $params['property_condition'] ?? 8,
        ]);

        return [
            'cohort_type' => 'lease',
            'projections' => [
                'annual_gross_rent' => round($annualGrossRent, 2),
                'annual_noi' => round($annualNOI, 2),
                'total_noi' => round($totalNOI, 2),
                'estimated_exit_value' => round($exitValue, 2),
                'total_proceeds' => round($totalProceeds, 2),
                'total_return_percent' => round($totalReturn, 2),
                'annualized_return_percent' => round($annualizedReturn, 2),
            ],
            'member_projections' => [
                'share_percent' => round($memberSharePercent * 100, 2),
                'total_noi_share' => round($memberNOI, 2),
                'exit_value_share' => round($memberExitValue, 2),
                'total_return_percent' => round($memberTotalReturn, 2),
                'absolute_profit' => round($memberNOI + $memberExitValue - $memberContribution, 2),
            ],
            'risk_analysis' => [
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
                'key_risks' => $this->identifyKeyRisks($params, 'lease'),
            ],
            'scenarios' => $this->runScenarios($params, 'lease'),
            'sensitivity_factors' => $this->calculateSensitivity($params, 'lease'),
        ];
    }

    /**
     * Run resale (flip) cohort simulation
     */
    public function simulateResaleCohort(array $params): array
    {
        $purchasePrice = $params['purchase_price'];
        $acquisitionCosts = $params['acquisition_costs'] ?? $purchasePrice * 0.03;
        $renovationBudget = $params['renovation_budget'];
        $holdingCosts = $params['holding_costs'] ?? 5000; // per month
        $holdingMonths = $params['holding_months'];
        $salesCosts = $params['sales_costs'] ?? 0; // calculated as % of resale
        $targetResalePrice = $params['target_resale_price'];
        $adminSetupFee = $params['admin_setup_fee'] ?? $purchasePrice * 0.02;
        $adminExitFee = $params['admin_exit_fee'] ?? $targetResalePrice * 0.03;
        $memberContribution = $params['member_contribution'];
        $memberTier = $params['member_tier'] ?? 1;
        $totalCapital = $params['total_capital'] ?? $purchasePrice + $acquisitionCosts + $renovationBudget + ($holdingCosts * $holdingMonths);

        // Calculate total costs
        $totalInvestment = $purchasePrice + $acquisitionCosts + $renovationBudget + ($holdingCosts * $holdingMonths);
        
        // Calculate proceeds
        $salesCommission = $targetResalePrice * 0.06; // 6% typical
        $totalSalesCosts = $salesCommission + $salesCosts;
        $grossProceeds = $targetResalePrice;
        $netProceeds = $grossProceeds - $totalSalesCosts - $adminExitFee;

        // Total return
        $totalReturn = (($netProceeds - $totalInvestment) / $totalInvestment) * 100;
        $annualizedReturn = $totalReturn / ($holdingMonths / 12);

        // Member-specific
        $memberSharePercent = $memberContribution / $totalCapital;
        $memberNetProceeds = $netProceeds * $memberSharePercent;
        $memberProfit = $memberNetProceeds - $memberContribution;
        $memberTotalReturn = ($memberProfit / $memberContribution) * 100;

        // Risk score (flips are higher risk)
        $riskScore = $this->calculateRiskScore([
            'market_conditions' => $params['market_conditions'] ?? 5,
            'renovation_complexity' => $params['renovation_complexity'] ?? 6,
            'timeline_buffer' => $holdingMonths > 12 ? 3 : 8,
        ]);

        return [
            'cohort_type' => 'resale',
            'projections' => [
                'total_investment' => round($totalInvestment, 2),
                'target_resale_price' => round($targetResalePrice, 2),
                'sales_costs' => round($totalSalesCosts, 2),
                'net_proceeds' => round($netProceeds, 2),
                'total_return_percent' => round($totalReturn, 2),
                'annualized_return_percent' => round($annualizedReturn, 2),
            ],
            'member_projections' => [
                'share_percent' => round($memberSharePercent * 100, 2),
                'net_proceeds_share' => round($memberNetProceeds, 2),
                'profit' => round($memberProfit, 2),
                'total_return_percent' => round($memberTotalReturn, 2),
            ],
            'risk_analysis' => [
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
                'key_risks' => $this->identifyKeyRisks($params, 'resale'),
            ],
            'scenarios' => $this->runScenarios($params, 'resale'),
            'sensitivity_factors' => $this->calculateSensitivity($params, 'resale'),
        ];
    }

    /**
     * Run Monte Carlo simulation
     */
    public function runMonteCarloSimulation(array $params, string $cohortType, int $iterations = 10000): array
    {
        $results = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            // Randomize key variables within reasonable ranges
            $randomizedParams = $this->randomizeParameters($params, $cohortType);
            
            // Run simulation with randomized params
            $result = match($cohortType) {
                'utilization' => $this->simulateUtilizationCohort($randomizedParams),
                'lease' => $this->simulateLeaseCohort($randomizedParams),
                'resale' => $this->simulateResaleCohort($randomizedParams),
                default => null,
            };
            
            if ($result) {
                $results[] = $result['member_projections']['total_return_percent'];
            }
        }

        sort($results);
        
        return [
            'iterations' => $iterations,
            'mean_return' => round(array_sum($results) / count($results), 2),
            'median_return' => round($results[intval(count($results) * 0.50)], 2),
            'percentile_90' => round($results[intval(count($results) * 0.10)], 2),
            'percentile_75' => round($results[intval(count($results) * 0.25)], 2),
            'percentile_50' => round($results[intval(count($results) * 0.50)], 2),
            'percentile_25' => round($results[intval(count($results) * 0.75)], 2),
            'percentile_10' => round($results[intval(count($results) * 0.90)], 2),
            'best_case' => round(max($results), 2),
            'worst_case' => round(min($results), 2),
            'standard_deviation' => round($this->calculateStdDev($results), 2),
            'probability_of_loss' => round((count(array_filter($results, fn($r) => $r < 0)) / count($results)) * 100, 2),
        ];
    }

    /**
     * Calculate portfolio diversification analysis
     */
    public function analyzePortfolio(User $user, array $plannedCohorts = []): array
    {
        // Get user's active cohorts
        $activeCohorts = $user->cohorts()->where('status', 'active')->get();
        
        $totalExposure = 0;
        $assetClassBreakdown = [];
        $riskScores = [];
        $expectedReturns = [];

        foreach ($activeCohorts as $cohort) {
            $memberData = $cohort->members()->where('user_id', $user->id)->first();
            $contribution = $memberData?->pivot->capital_contribution ?? 0;
            
            $totalExposure += $contribution;
            
            $assetClass = $cohort->cohort_type;
            $assetClassBreakdown[$assetClass] = ($assetClassBreakdown[$assetClass] ?? 0) + $contribution;
            
            $riskScores[] = $cohort->risk_score ?? 5;
            $expectedReturns[] = ($cohort->expected_return ?? 15) * ($contribution / 100000); // Weighted
        }

        // Add planned cohorts
        foreach ($plannedCohorts as $planned) {
            $totalExposure += $planned['contribution'];
            $assetClassBreakdown[$planned['type']] = ($assetClassBreakdown[$planned['type']] ?? 0) + $planned['contribution'];
            $riskScores[] = $planned['risk_score'] ?? 5;
            $expectedReturns[] = $planned['expected_return'] * ($planned['contribution'] / 100000);
        }

        // Calculate metrics
        $avgRiskScore = count($riskScores) > 0 ? array_sum($riskScores) / count($riskScores) : 0;
        $portfolioExpectedReturn = $totalExposure > 0 ? (array_sum($expectedReturns) / $totalExposure) * 100 : 0;
        
        // Concentration analysis
        $topCohortPercent = count($activeCohorts) > 0 
            ? ($activeCohorts->sortByDesc('pivot.capital_contribution')->first()->pivot->capital_contribution / $totalExposure) * 100 
            : 0;

        return [
            'total_exposure' => round($totalExposure, 2),
            'number_of_cohorts' => count($activeCohorts) + count($plannedCohorts),
            'asset_class_breakdown' => $assetClassBreakdown,
            'average_risk_score' => round($avgRiskScore, 2),
            'portfolio_expected_return' => round($portfolioExpectedReturn, 2),
            'concentration_risk' => [
                'top_cohort_percent' => round($topCohortPercent, 2),
                'warning' => $topCohortPercent > 25 ? 'High concentration detected' : null,
            ],
            'recommendations' => $this->generatePortfolioRecommendations($assetClassBreakdown, $avgRiskScore, $totalExposure),
        ];
    }

    // Helper methods

    private function getTierFeeDiscount(int $tier): float
    {
        return match($tier) {
            1, 2 => 0,
            3 => 0.0025, // 0.25%
            4 => 0.005,  // 0.5%
            5 => 0.01,   // 1%
            default => 0,
        };
    }

    private function calculateRiskScore(array $factors): int
    {
        // Simplified risk scoring (1-10 scale)
        $score = 5; // Base moderate risk
        
        // Adjust based on factors (implementation varies by cohort type)
        foreach ($factors as $key => $value) {
            if (is_numeric($value)) {
                $score += ($value - 5) * 0.3; // Weight factor influence
            }
        }
        
        return max(1, min(10, intval(round($score))));
    }

    private function getRiskLevel(int $score): string
    {
        return match(true) {
            $score <= 3 => 'Low',
            $score <= 6 => 'Moderate',
            $score <= 8 => 'Elevated',
            default => 'High',
        };
    }

    private function identifyKeyRisks(array $params, string $type): array
    {
        // Return key risks based on cohort type and parameters
        $risks = [];
        
        if ($type === 'utilization' && ($params['utilization_rate'] ?? 0) < 0.6) {
            $risks[] = 'Low utilization rate may significantly impact returns';
        }
        
        if ($type === 'lease' && ($params['vacancy_rate'] ?? 0) > 0.10) {
            $risks[] = 'High vacancy rate increases income volatility';
        }
        
        if ($type === 'resale' && ($params['holding_months'] ?? 0) < 12) {
            $risks[] = 'Short timeline increases execution risk';
        }
        
        return $risks;
    }

    private function runScenarios(array $params, string $type): array
    {
        // Run optimistic, realistic, pessimistic scenarios
        $scenarios = [];
        
        $multipliers = [
            'optimistic' => ['revenue' => 1.2, 'expenses' => 0.9],
            'realistic' => ['revenue' => 1.0, 'expenses' => 1.0],
            'pessimistic' => ['revenue' => 0.8, 'expenses' => 1.15],
        ];
        
        foreach ($multipliers as $scenario => $multiplier) {
            $adjustedParams = $params;
            // Adjust key parameters based on scenario
            // (simplified - actual implementation would be more sophisticated)
            
            $scenarios[$scenario] = match($type) {
                'utilization' => $this->simulateUtilizationCohort($adjustedParams),
                'lease' => $this->simulateLeaseCohort($adjustedParams),
                'resale' => $this->simulateResaleCohort($adjustedParams),
                default => null,
            };
        }
        
        return $scenarios;
    }

    private function calculateSensitivity(array $params, string $type): array
    {
        // Calculate how changes in key variables affect returns
        // Placeholder - full implementation would test each variable independently
        return [
            'most_sensitive_to' => 'utilization_rate',
            'sensitivity_score' => 8.5,
        ];
    }

    private function randomizeParameters(array $params, string $type): array
    {
        // Add randomness to parameters for Monte Carlo
        // Typically vary ±20% from base case
        $randomized = $params;
        
        foreach ($params as $key => $value) {
            if (is_numeric($value) && !in_array($key, ['member_contribution', 'member_tier'])) {
                $variance = $value * 0.20; // ±20%
                $randomized[$key] = $value + (mt_rand(-100, 100) / 100) * $variance;
            }
        }
        
        return $randomized;
    }

    private function calculateStdDev(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / count($values);
        return sqrt($variance);
    }

    private function generatePortfolioRecommendations(array $assetClassBreakdown, float $avgRiskScore, float $totalExposure): array
    {
        $recommendations = [];
        
        if ($avgRiskScore > 7) {
            $recommendations[] = 'Consider adding low-risk cohorts to balance portfolio';
        }
        
        $dominantClass = array_key_first($assetClassBreakdown);
        $dominantPercent = ($assetClassBreakdown[$dominantClass] / $totalExposure) * 100;
        
        if ($dominantPercent > 60) {
            $recommendations[] = "Over-concentrated in {$dominantClass} ({$dominantPercent}%). Diversify into other asset classes.";
        }
        
        if ($totalExposure > 1000000) {
            $recommendations[] = 'Consider speaking with a financial advisor about tax optimization strategies';
        }
        
        return $recommendations;
    }
}
