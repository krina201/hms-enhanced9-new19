<?php
namespace App\Enums;
enum ApprovalStatusEnum: string {
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
}
