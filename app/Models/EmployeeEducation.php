<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEducation extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_education_id';
    
    protected $fillable = ['employee_id','school','course','degree','from','to', 'remarks'];
}
