<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraining extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_training_id';
    
    protected $fillable = ['employee_id','training_status_type_id','training_type_id','start_date','completion_date', 'course_title', 'course_url','description','credit_hours'];
}
