<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('visits', function(Blueprint $t){
            $t->index('visit_date'); $t->index('type'); $t->index('department'); $t->index('doctor_id'); $t->index('location_id');
        });
        Schema::table('bills', function(Blueprint $t){
            $t->index('patient_id'); $t->index('visit_id'); $t->index('status');
        });
        Schema::table('payments', function(Blueprint $t){
            $t->index('bill_id');
        });
        Schema::table('admissions', function(Blueprint $t){
            $t->index('patient_id'); $t->index('visit_id'); $t->index('status');
        });
    }
    public function down(): void {
        // Laravel can't drop index without names reliably if created without names; guard by schema inspection in real project.
    }
};
