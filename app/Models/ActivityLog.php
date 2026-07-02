<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity',
        'type',
        'user_id',
        'user_name',
        'pdv',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to write a new activity log entry based on the authenticated user.
     */
    public static function writeLog(string $activity, string $type, ?string $details = null, string $pdv = 'XPTO001'): self
    {
        $user = auth()->user();
        return self::create([
            'activity' => $activity,
            'type' => $type,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'pdv' => $pdv,
            'details' => $details,
        ]);
    }
}
