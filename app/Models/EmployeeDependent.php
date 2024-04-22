<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDependent extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_dependent_id';
    
    protected $fillable = ['employee_id','employee_family_id'];
}
