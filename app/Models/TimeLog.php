<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'time_log_id';
    
    protected $fillable = ['employee_id','date','day','type','time','location', 'latitude', 'longitude'];
}
