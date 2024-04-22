<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEmploymentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_employment_id';
    
    protected $fillable = ['employee_id','employment_type','shift_schedule','status','overtime_entitlement','employement_date', 'probation_end_date', 'employment_status','payroll_cycle','paymet_structure', 'termination_date'];
}
