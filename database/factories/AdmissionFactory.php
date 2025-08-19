<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admission;
class AdmissionFactory extends Factory {
    protected $model = Admission::class;
    public function definition() {
        $visit = \App\Models\Visit::factory()->create();
        return [
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'admit_date' => now()->subDay(),
            'discharge_date' => now(),
            'status' => \App\Enums\AdmissionStatusEnum::DISCHARGED,
            'ward' => '1', 'bed' => 'B1', 'notes' => 'N', 'diagnosis' => 'Dx', 'procedures' => 'Proc', 'instructions' => 'Instr'
        ];
    }
}