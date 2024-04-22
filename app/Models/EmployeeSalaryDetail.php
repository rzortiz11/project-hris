<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalaryDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_salary_id';
    
    protected $fillable = ['employee_id','type','basic_amount','montly_amount','yearly_amount'];
}
