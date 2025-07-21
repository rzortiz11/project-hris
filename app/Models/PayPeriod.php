<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'pay_period_id';
    
    protected $fillable = [
        'type','start_date','end_date','cut_off_date','created_by'
    ];
}
