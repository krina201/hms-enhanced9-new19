<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Visit;
class VisitFactory extends Factory {
    protected $model = Visit::class;
    public function definition() {
        return [
            'patient_id' => function(){ return \App\Models\Patient::factory()->create()->id; },
            'visit_no' => strtoupper($this->faker->bothify('V#######')),
            'visit_date' => now(),
            'type' => \App\Enums\VisitTypeEnum::OPD,
            'department' => 'Medicine',
        ];
    }
}