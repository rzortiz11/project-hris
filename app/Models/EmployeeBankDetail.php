<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBankDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_bank_id';

    protected $fillable = ['employee_id','bank_name','account_name','account_no'];
}
