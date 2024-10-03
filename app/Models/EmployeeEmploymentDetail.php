<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEmploymentDetail extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'work_schedule' => 'array',
    ];

    protected $primaryKey = 'employee_employment_id';
    
    protected $fillable = ['employee_id','employment_type','employment_category','time_in', 'time_out', 'overtime_entitlement','employement_date', 'probation_end_date', 'employment_status','payroll_cycle','payment_structure','company', 'work_schedule', 'work_arrangement', 'termination_date', 'seperation_date'];
}
