# Advanced Inventory/PO Enhancements

## Includes
- **GRN listing + detail + PDF**
- **Multi-batch partial receiving** with suggestions
- **Inventory dashboard widgets** (low stock, expired lots, pending GRNs)
- **PO in-form item editor** (add/remove/edit lines, auto totals)
- **Strict enforcement** that `RECEIVED` only after 100% receiving via GRNs

## Install
1. Merge `app/`, `resources/`, `routes/` into your project.
2. Require PDF lib (optional but recommended):
```
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```
3. Run migrations if you haven't added GRN tables yet.
4. In `routes/web.php`:
```php
require __DIR__.'/advanced.php';
```
5. Permissions (Spatie):
- `purchase orders.edit`, `purchase orders.receive`
- `grn.view`
- `dashboard.view` (optional; gate the route manually if you prefer)

## Usage
- **GRN List**: `/grn`
- **GRN Detail/PDF**: `/grn/{id}` and `/grn/{id}/print`
- **Receive PO (multi-batch)**: `/purchase-orders/{id}/receive`
- **Inventory Dashboard**: `/dashboard/inventory`
- **PO Editor**: open `/purchase-orders/{id}/edit` (existing route), manage `Items` table

## Notes
- Suggestions in receive UI show up to 5 batches with stock, ordered by soonest expiry.
- Strict enforcement: PO `RECEIVED` status is auto-downgraded to `PARTIALLY_RECEIVED` if any open qty remains.
- Extend suggestion logic to prefer location, supplier, or lot attributes as needed.
