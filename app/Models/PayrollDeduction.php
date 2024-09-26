<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDeduction extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'deduction_id';
    
    protected $fillable = [
        'payroll_id','name','amount'
    ];
}
