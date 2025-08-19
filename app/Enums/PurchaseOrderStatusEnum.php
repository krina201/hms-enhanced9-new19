<?php
namespace App\Enums;
enum PurchaseOrderStatusEnum: string {
    case DRAFT = 'DRAFT';
    case ORDERED = 'ORDERED';
    case PARTIALLY_RECEIVED = 'PARTIALLY_RECEIVED';
    case RECEIVED = 'RECEIVED';
    case CANCELLED = 'CANCELLED';
}
