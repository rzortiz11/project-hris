<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalaryDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_salary_id';
    
    protected $fillable = ['employee_id','name','type','amount','pay_period' ,'effective_date','expiration_date','is_taxable'];

    public function employee(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_id', 'employee_id');
    }
}
