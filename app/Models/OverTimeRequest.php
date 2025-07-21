<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverTimeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'over_time_id';
    
    protected $fillable = ['employee_id','job_position','type','date_filling','date_from','time_from','date_to','time_to', 'hours', 'remarks', 'approver_id','action_date','disapproved_reason','status'];

}
