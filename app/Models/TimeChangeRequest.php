<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeChangeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'time_change_id';
    
    protected $fillable = ['employee_id','date_filling','old_time_in','old_time_out','new_time_in','new_time_out','remarks', 'approver_id','action_date','disapproved_reason','status'];

}
