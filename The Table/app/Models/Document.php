<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'uploaded_by',
        'title',
        'description',
        'type',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'visibility',
        'status',
        'download_count',
        'last_accessed_at',
        'blockchain_hash',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
        $this->last_accessed_at = now();
        $this->save();
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
