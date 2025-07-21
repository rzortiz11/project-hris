<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContactPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_contact_people_id';
    
    protected $fillable = ['employee_id','name','relationship','mobile','telephone','email', 'address'];
}
