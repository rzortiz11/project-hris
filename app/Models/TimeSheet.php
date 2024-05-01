<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_training_id';
    
    protected $fillable = ['employee_id','date','shift_schedule','time_in','in_location', 'in_latitude', 'in_longitude','break_time_out','break_time_in','time_out','out_location','out_latitude','out_longitude','out_date','time_in_2','time_out_2','late_time','over_time','remarks'];
}
