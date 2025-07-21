<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOverTime extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_over_time_id';
    
    protected $fillable = ['over_time_id','employee_id','date_filling','date_from','time_from','date_to','time_to', 'hours', 'code', 'description','over_time_rate','hourly_rate','amount'];

}
