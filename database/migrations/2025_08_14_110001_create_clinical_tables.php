<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $t) {
            $t->id();
            $t->string('mrn')->unique();
            $t->string('first_name');
            $t->string('last_name')->nullable();
            $t->date('dob')->nullable();
            $t->string('gender')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->text('address')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->string('visit_no')->unique();
            $t->timestamp('visit_date')->nullable();
            $t->string('type'); // enum-like
            $t->unsignedBigInteger('doctor_id')->nullable();
            $t->string('department')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->text('chief_complaint')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('admissions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->timestamp('admit_date')->nullable();
            $t->timestamp('discharge_date')->nullable();
            $t->string('status')->default('ADMITTED');
            $t->string('ward')->nullable();
            $t->string('bed')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->text('notes')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('bills', function (Blueprint $t) {
            $t->id();
            $t->string('bill_no')->unique();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->timestamp('bill_date')->nullable();
            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('tax', 12, 2)->default(0);
            $t->decimal('discount', 12, 2)->default(0);
            $t->decimal('grand_total', 12, 2)->default(0);
            $t->string('status')->default('DRAFT');
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('bill_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('bill_id'); // no FK
            $t->string('description');
            $t->decimal('qty', 12, 3)->default(1);
            $t->decimal('unit_price', 12, 2)->default(0);
            $t->decimal('tax_rate', 6, 2)->default(0);
            $t->decimal('discount', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('bill_id'); // no FK
            $t->timestamp('payment_date')->nullable();
            $t->decimal('amount', 12, 2)->default(0);
            $t->string('method')->nullable();
            $t->string('reference')->nullable();
            $t->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->string('type')->default('MEDICINE');
            $t->text('text')->nullable();
            $t->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->string('test_name');
            $t->string('status')->default('ORDERED');
            $t->timestamp('ordered_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->string('result_path')->nullable();
            $t->timestamps();
        });

        Schema::create('diet_plans', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->string('type')->default('NORMAL');
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::create('feedback', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('patient_id'); // no FK
            $t->unsignedBigInteger('visit_id')->nullable(); // no FK
            $t->unsignedTinyInteger('rating');
            $t->text('comments')->nullable();
            $t->timestamps();
        });

        Schema::create('attachments', function (Blueprint $t) {
            $t->id();
            $t->morphs('attachable');
            $t->string('path');
            $t->string('original_name')->nullable();
            $t->string('mime')->nullable();
            $t->unsignedBigInteger('size')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (
            [
                'attachments',
                'feedback',
                'diet_plans',
                'lab_tests',
                'prescriptions',
                'payments',
                'bill_items',
                'bills',
                'admissions',
                'visits',
                'patients'
            ] as $t
        ) {
            Schema::dropIfExists($t);
        }
    }
};
