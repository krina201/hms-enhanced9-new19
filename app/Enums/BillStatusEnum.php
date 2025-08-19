<?php
namespace App\Enums;
enum BillStatusEnum: string { case DRAFT='DRAFT'; case ISSUED='ISSUED'; case PAID='PAID'; case PARTIAL='PARTIAL'; case VOID='VOID'; }
