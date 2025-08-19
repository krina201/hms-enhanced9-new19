<?php

namespace App\Console\Commands;

use App\Support\TenancyScan;

use Illuminate\Console\Command;
use App\Models\Requisition;
use App\Models\RequisitionApproval;
use App\Models\ApprovalStep;
use App\Models\RequisitionApprovalEvent;
use App\Enums\ApprovalStatusEnum;
use App\Notifications\SlaBreachedNotification;

class CheckSlaBreaches extends Command
{
    protected $signature = 'hms:check-sla-breaches {--dry-run}';
    protected $description = 'Scan pending approvals and notify if SLA is breached';

    public function handle(): int
    {
        $totalChecked = 0;
        $totalEscalated = 0;
        TenancyScan::forEachTenant(function ($tenant) use (&$totalChecked, &$totalEscalated) {
            $pending = RequisitionApproval::with('requisition', 'step')
                ->where('status', ApprovalStatusEnum::PENDING)->get();

            $count = 0;
            foreach ($pending as $pa) {
                $sla = (int) ($pa->step->sla_hours ?? 0);
                if ($sla <= 0) continue;

                $ageHours = now()->diffInHours($pa->created_at);
                if ($ageHours <= $sla) continue;

                $count++;

                // Log event
                if (!$this->option('dry-run')) {
                    RequisitionApprovalEvent::create([
                        'requisition_approval_id' => $pa->id,
                        'action' => 'escalated',
                        'actor_id' => null,
                        'note' => "SLA breached by " . max(0, $ageHours - $sla) . " hour(s)",
                        'meta' => ['age_hours' => $ageHours, 'sla_hours' => $sla],
                    ]);
                }

                // Notify role holders
                if (!$this->option('dry-run')) {
                    $role = $pa->step->role_name;
                    // users with this role
                    $users = \App\Models\User::role($role)->get();
                    foreach ($users as $u) {
                        $u->notify(new SlaBreachedNotification('requisition', $pa->requisition_id, $role, $ageHours - $sla));
                    }
                    // also notify Admin/Procurement roles if present
                    foreach (['Procurement Manager', 'Admin', 'Super Admin'] as $r) {
                        try {
                            $users = \App\Models\User::role($r)->get();
                            foreach ($users as $u) {
                                $u->notify(new SlaBreachedNotification('requisition', $pa->requisition_id, $role, $ageHours - $sla, "Escalated to {$r}"));
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            $totalChecked += $pending->count();
            $totalEscalated += $count;
        });
        $this->info("Checked {$totalChecked} pending; escalated {$totalEscalated}.");
        return self::SUCCESS;
    }
}
