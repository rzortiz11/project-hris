<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSelfService extends Employee
{
    use HasFactory;

    protected $table = 'employees';
}
