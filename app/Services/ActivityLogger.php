<?php
namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public function log(string $module, string $action, ?int $referenceId = null, array $changes = []): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => $module,
            'action' => $action,
            'reference_id' => $referenceId,
            'changes' => $changes,
            'ip' => request()->ip(),
        ]);
    }
}
