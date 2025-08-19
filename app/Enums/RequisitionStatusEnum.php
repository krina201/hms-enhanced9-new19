<?php
namespace App\Enums;
enum RequisitionStatusEnum: string {
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case CONVERTED = 'CONVERTED';
}
