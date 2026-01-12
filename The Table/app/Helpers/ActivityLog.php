<?php

namespace App\Helpers;

use App\Models\ActivityLog as ActivityLogModel;
use Illuminate\Support\Facades\Auth;

class ActivityLog
{
    /**
     * Log an activity
     *
     * @param string $action
     * @param int|null $userId
     * @param int|null $relatedId
     * @param string|null $description
     * @return ActivityLogModel
     */
    public static function log(string $action, ?int $userId = null, ?int $relatedId = null, ?string $description = null): ActivityLogModel
    {
        return ActivityLogModel::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'related_id' => $relatedId,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
