<?php
namespace App\Services;

use App\Models\Requisition;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\RequisitionApproval;
use App\Models\RequisitionApprovalEvent;
use App\Enums\RequisitionStatusEnum;
use App\Enums\ApprovalStatusEnum;

class ApprovalService
{
    public function startRequisitionWorkflow(Requisition $req, ?ApprovalWorkflow $wf = null): void
    {
        if ($req->workflow_id) return;
        if (!$wf) {
            $wf = ApprovalWorkflow::where('applies_to','requisition')->where('is_active',true)->orderBy('id')->first();
            if (!$wf) return;
        }
        $req->workflow_id = $wf->id;
        $req->save();

        foreach ($wf->steps as $step) {
            $ap = RequisitionApproval::create([
                'requisition_id' => $req->id,
                'approval_step_id' => $step->id,
                'status' => ApprovalStatusEnum::PENDING,
            ]);
            RequisitionApprovalEvent::create([
                'requisition_approval_id' => $ap->id,
                'action' => 'pending_created',
                'actor_id' => auth()->id(),
                'note' => null,
            ]);
        }
        $req->status = RequisitionStatusEnum::SUBMITTED;
        $req->save();
    }

    public function currentPendingStep(Requisition $req): ?RequisitionApproval
    {
        return RequisitionApproval::where('requisition_id', $req->id)
            ->where('status', ApprovalStatusEnum::PENDING)
            ->orderBy('approval_step_id')->first();
    }

    public function approveCurrentStep(Requisition $req, int $userId, string $userRole = ''): bool
    {
        $current = $this->currentPendingStep($req);
        if (!$current) return false;

        $requiredRole = $current->step->role_name;
        if (!(auth()->user()?->hasRole($requiredRole))) {
            return false;
        }

        $current->status = ApprovalStatusEnum::APPROVED;
        $current->approved_by = $userId;
        $current->approved_at = now();
        $current->save();

        RequisitionApprovalEvent::create([
            'requisition_approval_id' => $current->id,
            'action' => 'approved',
            'actor_id' => $userId,
            'note' => "Approved by user #{$userId}",
        ]);

        $next = $this->currentPendingStep($req);
        if (!$next) {
            $req->status = RequisitionStatusEnum::APPROVED;
            $req->save();
        }
        return true;
    }

    public function rejectCurrentStep(Requisition $req, int $userId, string $note = null): bool
    {
        $current = $this->currentPendingStep($req);
        if (!$current) return false;

        $current->status = ApprovalStatusEnum::REJECTED;
        $current->approved_by = $userId;
        $current->approved_at = now();
        $current->save();

        RequisitionApprovalEvent::create([
            'requisition_approval_id' => $current->id,
            'action' => 'rejected',
            'actor_id' => $userId,
            'note' => $note,
        ]);

        $req->status = RequisitionStatusEnum::REJECTED;
        $req->save();
        return true;
    }
}
