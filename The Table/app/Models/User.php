<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'id_number',
        'kyc_id_number',
        'id_document',
        'proof_of_address',
        'kyc_status',
        'kyc_notes',
        'kyc_verified_at',
        'kyc_submitted_at',
        'kyc_rejection_reason',
        'kyc_id_document_front',
        'kyc_id_document_back',
        'kyc_proof_of_residence',
        'bank_name',
        'account_number',
        'account_holder_name',
        'bank_code',
        'branch_code',
        'crypto_wallet_address',
        'crypto_network',
        'crypto_wallet_verified_at',
        'bio',
        'avatar',
        'country',
        'city',
        'address',
        'postal_code',
        'two_factor_enabled',
        'two_factor_secret',
        'last_login_at',
        'last_login_ip',
        'kyc_verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'kyc_submitted_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function adminedCohorts(): HasMany
    {
        return $this->hasMany(Cohort::class, 'admin_id');
    }

    public function cohorts()
    {
        return $this->belongsToMany(Cohort::class, 'cohort_user')
            ->withPivot('contribution_amount', 'joined_at', 'status')
            ->withTimestamps();
    }

    public function cohortMemberships(): HasMany
    {
        return $this->hasMany(CohortMember::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function cohortFunds(): HasMany
    {
        return $this->hasMany(CohortFund::class);
    }

    /**
     * Get the user's crypto wallets
     */
    public function cryptoWallets(): HasMany
    {
        return $this->hasMany(CryptoWallet::class);
    }

    // Get or create wallet for user
    public function getOrCreateWallet(): Wallet
    {
        if (!$this->wallet) {
            $wallet = Wallet::create([
                'user_id' => $this->id,
            ]);
            $this->load('wallet');
            return $wallet;
        }
        return $this->wallet;
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }
        return "{$this->first_name} {$this->last_name}";
    }

    public function getNameAttribute($value)
    {
        return $value ?? "{$this->first_name} {$this->last_name}";
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPlatformAdmin(): bool
    {
        return $this->role === 'platform_admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isKYCVerified(): bool
    {
        return in_array($this->kyc_status, ['approved', 'verified']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canParticipateInCohorts(): bool
    {
        return $this->isActive() && $this->isKYCVerified();
    }

    public function totalInvestedCapital(): int
    {
        return $this->cohortMemberships()
            ->where('status', 'active')
            ->sum('capital_committed');
    }

    public function activeCohorts()
    {
        return $this->cohortMemberships()
            ->where('status', 'active')
            ->with('cohort')
            ->get()
            ->pluck('cohort');
    }

    // Scopes
    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeKYCVerified($query)
    {
        return $query->where('kyc_status', 'approved');
    }
}
