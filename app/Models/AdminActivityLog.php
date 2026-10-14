<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * A basic record of Admin Management actions (Sub Admin created, permissions
 * changed, disabled/enabled, deleted, password reset) so the Main Admin can
 * see what changed and by whom. No `updated_at` — entries are write-once.
 */
#[Fillable(['actor_id', 'action', 'target_email', 'details'])]
class AdminActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'admin_activity_log';

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public static function record(string $action, ?string $targetEmail = null, ?string $details = null): void
    {
        static::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'target_email' => $targetEmail,
            'details' => $details,
        ]);
    }
}
