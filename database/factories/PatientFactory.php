<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient;
class PatientFactory extends Factory {
    protected $model = Patient::class;
    public function definition() {
        return [
            'mrn' => 'MRN'.$this->faker->unique()->numerify('####'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->date('Y-m-d', '-10 years'),
            'gender' => \App\Enums\PatientGenderEnum::MALE,
        ];
    }
}