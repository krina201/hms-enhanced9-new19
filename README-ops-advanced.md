# Procurement & Inventory Advanced Pack 2

## Features
- **GRN filters + CSV export** (date range, PO, supplier, location)
- **Batch/Lot barcode labels (PDF/HTML)**: `/batches/labels?ids=1,2,3`
- **Stock aging report + soon-to-expire alerts**: `/reports/stock-aging`
- **Goods Return (negative adjustment)**: `/returns` and `/returns/create`
- **Requisition → PO with approvals**: `/requisitions` and `/requisitions/{id}/edit`

## Install
1. Merge into your Laravel app.
2. Add routes in `routes/web.php`:
```php
require __DIR__.'/ops-advanced.php';
```
3. Run migrations:
```
php artisan migrate
```
4. Optional packages:
```
composer require barryvdh/laravel-dompdf simplesoftwareio/simple-qrcode
```
5. Permissions to seed/assign:
- `grn.export`
- `returns.create`
- `requisitions.edit`, `requisitions.approve`, `requisitions.convert`

## Usage Tips
- Use the **GRN** page filters then click **Export CSV**.
- Generate **labels** by selecting batch IDs (you can add checkboxes beside batches in your UI and call the label route).
- **Stock Aging** shows expiring/expired counts and can export CSV.
- **Returns** decrement batch quantities and insert negative `ADJUST` movements.
- **Requisitions** can be submitted, approved, and converted to PO (one PO per supplier group).
