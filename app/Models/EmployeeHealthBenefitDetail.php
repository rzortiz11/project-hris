<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeHealthBenefitDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_health_benefit_id';
    
    protected $fillable = ['employee_id','name','enrollment_date','coverage_start_date','coverage_end_date','monthly_premium'];
}
