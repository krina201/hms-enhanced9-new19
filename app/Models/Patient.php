<?php
/**
 * Eloquent model representing a patient record (MRN, demographics, contacts).
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PatientGenderEnum;

class Patient extends Model {
    use SoftDeletes;
    protected $fillable=['mrn','first_name','last_name','dob','gender','phone','email','address'];
    protected $casts=['dob'=>'date','gender'=>PatientGenderEnum::class];
    public function visits(): HasMany { return $this->hasMany(Visit::class); }
    public function bills(): HasMany { return $this->hasMany(Bill::class); }
}