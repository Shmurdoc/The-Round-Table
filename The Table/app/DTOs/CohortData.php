<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class CohortData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $cohortClass,
        public ?string $assetType,
        public int $minimumViableCapital,
        public int $idealTarget,
        public int $hardCap,
        public int $minContribution,
        public int $maxContribution,
        public int $durationMonths,
        public ?float $projectedAnnualReturn,
        public ?float $expectedTotalPayout,
        public string $riskLevel = 'moderate',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title', $request->input('name', '')),
            description: $request->input('description', ''),
            cohortClass: $request->input('cohort_class', 'growth'),
            assetType: $request->input('asset_type'),
            minimumViableCapital: (int) (($request->input('minimum_viable_capital', $request->input('contribution_amount', 0))) * 100),
            idealTarget: (int) (($request->input('ideal_target', $request->input('contribution_amount', 0) * $request->input('max_members', 10))) * 100),
            hardCap: (int) (($request->input('hard_cap', $request->input('contribution_amount', 0) * $request->input('max_members', 10))) * 100),
            minContribution: (int) (($request->input('min_contribution', $request->input('contribution_amount', 0))) * 100),
            maxContribution: (int) (($request->input('max_contribution', $request->input('contribution_amount', 0))) * 100),
            durationMonths: (int) $request->input('duration_months', 12),
            projectedAnnualReturn: $request->filled('projected_annual_return') ? (float) $request->input('projected_annual_return') : null,
            expectedTotalPayout: $request->filled('expected_total_payout') ? (float) $request->input('expected_total_payout') : null,
            riskLevel: $request->input('risk_level', 'moderate'),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'cohort_class' => $this->cohortClass,
            'asset_type' => $this->assetType,
            'minimum_viable_capital' => $this->minimumViableCapital,
            'ideal_target' => $this->idealTarget,
            'hard_cap' => $this->hardCap,
            'min_contribution' => $this->minContribution,
            'max_contribution' => $this->maxContribution,
            'duration_months' => $this->durationMonths,
            'projected_annual_return' => $this->projectedAnnualReturn,
            'risk_level' => $this->riskLevel,
        ];
    }
}
