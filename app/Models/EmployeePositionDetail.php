<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePositionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_position_id';
    
    protected $fillable = ['employee_id','job_position','job_category','job_description','joined_designation','reporting_person', 'reporting_designation', 'location'];
}
