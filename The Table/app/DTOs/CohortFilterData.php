<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class CohortFilterData
{
    public function __construct(
        public ?string $status = null,
        public ?string $cohortClass = null,
        public ?int $minContribution = null,
        public ?int $maxContribution = null,
        public ?string $search = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: $request->input('status'),
            cohortClass: $request->input('cohort_class'),
            minContribution: $request->filled('min_contribution') ? (int) ($request->input('min_contribution') * 100) : null,
            maxContribution: $request->filled('max_contribution') ? (int) ($request->input('max_contribution') * 100) : null,
            search: $request->input('search'),
        );
    }
}
