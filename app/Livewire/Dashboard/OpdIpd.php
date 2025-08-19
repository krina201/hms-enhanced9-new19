<?php
/**
 * Livewire dashboard component for OPD/IPD counters and charts.
 *
 * @package HMS
 */
namespace App\Livewire\Dashboard;
use Livewire\Component;
use App\Models\Visit;
use App\Enums\VisitTypeEnum;

class OpdIpd extends Component
{
    public string $from; public string $to; public ?string $department=null; public ?string $doctor=null; public ?string $location=null;

    public function mount(){ $this->from = now()->subDays(30)->toDateString(); $this->to = now()->toDateString(); }

    public function render()
    {
        $doctorId = is_numeric($this->doctor ?? null) ? (int)$this->doctor : null; $locationId = is_numeric($this->location ?? null) ? (int)$this->location : null;
        $base = Visit::query()->whereBetween('visit_date', [$this->from.' 00:00:00', $this->to.' 23:59:59'])
            ->when($this->department, fn($q)=>$q->where('department',$this->department))
            ->when($doctorId, fn($q)=>$q->where('doctor_id',$doctorId))
            ->when($locationId, fn($q)=>$q->where('location_id',$locationId));

        $opd = (clone $base)->where('type', VisitTypeEnum::OPD)->count();
        $ipd = (clone $base)->where('type', VisitTypeEnum::IPD)->count();

        $daily = (clone $base)->selectRaw('DATE(visit_date) d, COUNT(*) c')->groupBy('d')->orderBy('d')->get();
        $dept = (clone $base)->selectRaw('COALESCE(department, "Unknown") d, COUNT(*) c')->groupBy('d')->orderByDesc('c')->limit(10)->get();

        return view('livewire.dashboard.opd_ipd', ['opd'=>$opd,'ipd'=>$ipd,'daily'=>$daily,'dept'=>$dept]);
    }
}