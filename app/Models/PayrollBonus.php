<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollBonus extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'bonuses_id';
    
    protected $fillable = [
        'payroll_id','name','is_taxable','amount'
    ];
}
