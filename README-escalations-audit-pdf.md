# Escalations + Approval Audit + Signed PDFs

## Features
- **SLA escalations**: scheduled command scans pending approval steps where `created_at + sla_hours < now()` and sends notifications.
- **Audit trail per step**: every state transition (pending_created, approved, rejected, escalated) is recorded.
- **Signed PDFs & Terms**: PO/GRN PDFs now show branding, terms, and signature blocks (images pulled from settings if available).

## Install
1. Merge files.
2. Run migrations:
```
php artisan migrate
```
3. Add the command to your scheduler in `app/Console/Kernel.php`:
```php
protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
{
    $schedule->command('hms:check-sla-breaches')->everyFifteenMinutes()->withoutOverlapping();
}
```
Then ensure your cron is set (typical for Laravel):
```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

4. Ensure your `User` model uses `Notifiable` and Spatie `HasRoles`.

5. Optional packages for PDF and notifications (if not already installed):
```
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## Settings (optional)
Provide these keys in your `settings` table (if you use a `Setting` model):
- `hospital_name`, `hospital_logo_url`, `hospital_address`
- `po_terms`, `grn_terms`
- `signature_prepared_by_url`, `signature_approved_by_url`

## Usage
- Approve/reject from the Requisition form; events appear under **Approval Audit Trail**.
- If a step exceeds its SLA, assigned role holders and Admin roles receive notifications, and an **escalated** event is logged.
- Print **PO** and **GRN** PDFs to see **terms** and **signature** blocks.

## Extend
- Add a nightly digest: schedule `hms:check-sla-breaches --dry-run` piped to email/report.
- Add Slack channel: create a Slack notifier in `SlaBreachedNotification::via()` when your Slack integration is ready.
