<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEmploymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employment_history_id';
    
    protected $fillable = ['employee_id','company_name','job_title','job_description', 'start_date', 'end_date'];
}
