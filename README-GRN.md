# Partial Receiving (GRN) + Permissions + Activity Logs

## What’s included
- **Enums**: `PurchaseOrderStatusEnum` now includes `PARTIALLY_RECEIVED`
- **Models**: `GoodsReceipt`, `GoodsReceiptItem`
- **Migrations**: `goods_receipts`, `goods_receipt_items`
- **Services**:
  - `ReceivingService::postGoodsReceipt(GoodsReceipt $grn)`
  - `ActivityLogger::log(...)`
- **Livewire**:
  - `PurchaseOrder/Receive` component + Blade (`/purchase-orders/{id}/receive`)
  - Updated `PurchaseOrder/Form` (auto-calculates totals from PO items; permissions; activity logging)
  - Updated `Ticket/Form` (permissions + activity logs for save/comment/delete)

## Usage
1. Merge files into your project.
2. Run migrations: `php artisan migrate`
3. Ensure Spatie permissions include:
   - `purchase orders.edit`, `purchase orders.receive`
   - `tickets.edit`, `tickets.comment`, `tickets.delete_comment`
4. Open a PO and go to:
   - `/purchase-orders/{id}/receive` to create a GRN with per-line `received_qty`, `batch_no`, `expiry_date`.
5. Posting the GRN will:
   - Create/find batches
   - Insert `RESTOCK` movements
   - Increment `qty_on_hand`
   - Flip PO status to `PARTIALLY_RECEIVED` or `RECEIVED`
6. Edit PO: server now **recomputes totals** from PO items.

## Notes
- If you already customized `ReceivingService`, replace only the `postGoodsReceipt` method or adapt accordingly.
- Extend the receive UI to support serial/lot fields, attachments (PDF invoices), or QA flags if needed.
