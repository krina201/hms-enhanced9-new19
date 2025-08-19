<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bill;
class BillFactory extends Factory {
    protected $model = Bill::class;
    public function definition() {
        return [
            'bill_no' => 'B'.$this->faker->unique()->bothify('#######'),
            'patient_id' => function(){ return \App\Models\Patient::factory()->create()->id; },
            'bill_date' => now(),
            'status' => \App\Enums\BillStatusEnum::ISSUED,
            'subtotal' => 0, 'tax' => 0, 'discount' => 0, 'grand_total' => 0,
        ];
    }
}