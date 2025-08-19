# Branding + Approvals Pack

## What's inside
- **GRN advanced filters**: supplier name search on GRN index.
- **PDF branding** for GRN and PO with logo/name/address from DB settings (fallback to app config).
- **Batch labels with Code128** barcodes (or QR fallback).
- **Multi-step approvals** for Requisitions using workflows, steps, roles, and SLA hours.

## Install
1. Merge into your project.
2. Add routes:
```php
require __DIR__.'/branding-approvals.php';
```
3. Optional packages:
```
composer require barryvdh/laravel-dompdf picqer/php-barcode-generator simplesoftwareio/simple-qrcode
```
4. Migrate:
```
php artisan migrate
```
5. Seed an approval workflow:
```php
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
$wf = ApprovalWorkflow::create(['name'=>'Default Requisition','applies_to'=>'requisition','is_active'=>true]);
ApprovalStep::create(['approval_workflow_id'=>$wf->id,'level'=>1,'role_name'=>'Department Head','sla_hours'=>24]);
ApprovalStep::create(['approval_workflow_id'=>$wf->id,'level'=>2,'role_name'=>'Procurement Manager','sla_hours'=>48]);
```
6. Ensure users have matching **Spatie roles** (`Department Head`, `Procurement Manager`, etc.).

## Usage
- **GRN list** now filters by supplier **name** (and ID), plus date range, PO, location.
- **Print PO**: `/purchase-orders/{id}/print`
- **Print GRN**: `/grn/{id}/print`
- **Labels**: `/batches/labels?ids=1,2,3` (uses Code128 if `picqer` installed, else QR if `simple-qrcode` installed)
- **Requisition approvals**:
  - Create workflow + steps (seed snippet above)
  - In Requisition form: click **Submit** to start workflow
  - Users with the required **role** can click **Approve Current Step** or **Reject Current Step**
  - When all steps approved, status flips to **APPROVED**

## Notes
- SLAs are stored per step (`sla_hours`) for reporting/escalations. Add scheduled jobs to scan `requisition_approvals` where `status=PENDING` and step SLA breached.
- Branding reads from `Setting` model keys: `hospital_name`, `hospital_logo_url`, `hospital_address` if present.
