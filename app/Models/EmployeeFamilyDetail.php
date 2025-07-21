<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFamilyDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_family_id';
    
    protected $fillable = ['employee_id','name','birthdate','occupation','employeer','mobile', 'address', 'relationship','anniversary', 'school', 'is_alive', 'is_disabled', 'is_medical_entitled', 'is_dependent', 'is_adopted'];
}
