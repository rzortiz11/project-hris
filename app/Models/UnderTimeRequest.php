<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnderTimeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'under_time_id';
    
    protected $fillable = ['employee_id','date_filling','type','date','time_out','remarks', 'approver_id','action_date','disapproved_reason','status'];

}
