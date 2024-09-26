<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollEmployeeContribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_contribution_id';
    
    protected $fillable = [
        'payroll_id','sss_contribution','philhealth_contribution', 'pagibig_contribution'
    ];
}
