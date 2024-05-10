<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_id';
    
    protected $fillable = ['employee_reference','user_id','title','gender','birthdate','religion', 'nationality', 'picture','is_active', 'progress','created_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function contact(): HasOne
    {
        return $this->hasOne(EmployeeContactDetail::class, 'employee_id', 'employee_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(EmployeeContactAddress::class, 'employee_id', 'employee_id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmployeeContactPerson::class, 'employee_id', 'employee_id');
    }

    public function position(): HasOne
    {
        return $this->hasOne(EmployeePositionDetail::class, 'employee_id', 'employee_id');
    }

    public function employment(): HasOne
    {
        return $this->hasOne(EmployeeEmploymentDetail::class, 'employee_id', 'employee_id');
    }

    public function issued_items(): HasMany {
        return $this->hasMany(EmployeeIssuedItem::class, 'employee_id', 'employee_id');
    }

    public function salary(): HasMany
    {
        return $this->hasMany(EmployeeSalaryDetail::class, 'employee_id', 'employee_id');
    }

    public function salaryView(): HasMany
    {
        return $this->hasMany(EmployeeSalaryDetail::class, 'employee_id', 'employee_id');
    }

    public function family(): HasMany {
        return $this->hasMany(EmployeeFamilyDetail::class, 'employee_id', 'employee_id');
    }

    public function employeeFather(): HasOne {
        return $this->hasOne(EmployeeFamilyDetail::class, 'employee_id', 'employee_id')
            ->where('relationship', 'FATHER');
    }

    public function employeeMother(): HasOne {
        return $this->hasOne(EmployeeFamilyDetail::class, 'employee_id', 'employee_id')
            ->where('relationship', 'MOTHER');
    }

    public function employeeSpouse(): HasOne {
        return $this->hasOne(EmployeeFamilyDetail::class, 'employee_id', 'employee_id')
            ->where('relationship', 'SPOUSE');
    }

    public function employeeChildren(): HasMany{
        return $this->hasMany(EmployeeFamilyDetail::class, 'employee_id', 'employee_id')
            ->where('relationship', 'CHILD');
    }

    public function healthBenefits(): HasMany
    {
        return $this->hasMany(EmployeeHealthBenefitDetail::class, 'employee_id', 'employee_id');
    }

    public function dependents(): HasMany {
        return $this->hasMany(EmployeeDependent::class, 'employee_id', 'employee_id');
    }

    public function trainings(): HasMany {
        return $this->hasMany(EmployeeTraining::class, 'employee_id', 'employee_id');
    }

    public function id_details(): HasOne {
        return $this->hasOne(EmployeeIdDetail::class, 'employee_id', 'employee_id');
    }

    public function bank(): HasOne {
        return $this->hasOne(EmployeeBankDetail::class, 'employee_id', 'employee_id');
    }

    public function employeeDocuments(): HasMany {
        return $this->hasMany(EmployeeDocument::class, 'employee_id', 'employee_id');
    }

    public function education(): HasMany {
        return $this->hasMany(EmployeeEducation::class, 'employee_id', 'employee_id');
    }

    public function employment_history(): HasMany {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'employee_id', 'employee_id');
    }

    public function employee_timesheets(): HasMany {
        return $this->hasMany(TimeSheet::class, 'employee_id', 'employee_id');
    }

    public function employee_timelogs(): HasMany {
        return $this->hasMany(TimeLog::class, 'employee_id', 'employee_id');
    }

    public function employee_leave_balances(): HasMany {
        return $this->hasMany(EmployeeLeaveBalance::class, 'employee_id', 'employee_id');
    }
}
