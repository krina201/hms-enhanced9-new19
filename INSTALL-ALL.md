
# HMS — One Bundle (All Features)

This bundle merges all previously delivered packs in the correct order. Later packs override earlier files when needed.

## Packs merged (order of precedence: last wins)
1) hms-receiving-grn-aug13.zip
2) hms-advanced-pack-aug13.zip
3) hms-procurement-advanced2-aug13.zip
4) hms-approval-branding-pack-aug13.zip
5) hms-escalations-audit-pdf-aug13.zip

## How to install

1. **Backup** your project first.
2. Unzip this bundle into your Laravel app root (it contains `app/`, `resources/`, `routes/`, etc.).
3. Add these lines to `routes/web.php` (if not already present):
```php
require __DIR__.'/modules-receive.php';      // From GRN receiving
require __DIR__.'/advanced.php';             // Inventory widgets, GRN list/detail, PO editor
require __DIR__.'/ops-advanced.php';         // Reports, returns, requisitions, labels, filters & exports
require __DIR__.'/branding-approvals.php';   // PO print route
```
4. Install optional packages as needed:
```
composer require barryvdh/laravel-dompdf picqer/php-barcode-generator simplesoftwareio/simple-qrcode
```
5. Run migrations:
```
php artisan migrate
```
6. (Optional) Seed permissions/roles and approvals workflow:
   - Permissions: `purchase orders.edit`, `purchase orders.receive`, `grn.export`, `returns.create`, `requisitions.edit`, `requisitions.approve`, `requisitions.convert`
   - Roles for approvals (examples): `Department Head`, `Procurement Manager`
7. Schedule SLA checker in `app/Console/Kernel.php`:
```php
protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
{
    $schedule->command('hms:check-sla-breaches')->everyFifteenMinutes()->withoutOverlapping();
}
```
Ensure your cron runs Laravel's scheduler.
8. Configure branding & docs settings (if you have a `Setting` model):
   - `hospital_name`, `hospital_logo_url`, `hospital_address`
   - `po_terms`, `grn_terms`
   - `signature_prepared_by_url`, `signature_approved_by_url`

## Notes
- Multi-batch partial receiving, GRN lists/detail/PDFs, inventory widgets, stock aging & alerts, returns (negative adjustments), requisition→PO with multi-step approvals, GRN advanced filters, branded PDFs with signatures/terms, SLA escalations & audit trails are included.
- If you already have some of these files, the versions here are intended to be the **latest**.


## Security Hardening Notes (added)
- All routes are now grouped under `['web','auth','tenant']`.
- Sensitive routes have permission middleware (`purchase orders.print`, `grn.view`, `labels.print`).
- PDFs only embed remote images from hosts listed in `PDF_ALLOWED_HOSTS`.
- Inventory updates use row locks and prevent negative stock unless `INVENTORY_ALLOW_NEGATIVE_STOCK=true`.
- SLA checks run per-tenant via `App\Support\TenancyScan`.

## Recommended include
In `routes/web.php` include the monolithic file:
```php
require __DIR__.'/hms_all.php';
```
