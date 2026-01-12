<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'investment_type',
        'amount',
        'current_value',
        'status',
        'description',
        'investment_date',
        'maturity_date',
        'expected_return',
        'metadata',
    ];

    protected $casts = [
        'investment_date' => 'date',
        'maturity_date' => 'date',
        'amount' => 'decimal:2',
        'current_value' => 'decimal:2',
        'expected_return' => 'decimal:4',
        'metadata' => 'json',
    ];

    // Relationships
    public function cohort()
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
