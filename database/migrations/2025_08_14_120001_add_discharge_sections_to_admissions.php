<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('admissions', function(Blueprint $t){
            $t->text('diagnosis')->nullable()->after('notes');
            $t->text('procedures')->nullable()->after('diagnosis');
            $t->text('instructions')->nullable()->after('procedures');
        });
    }
    public function down(): void {
        Schema::table('admissions', function(Blueprint $t){
            $t->dropColumn(['diagnosis','procedures','instructions']);
        });
    }
};
