<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeManagement extends Employee
{
    use HasFactory;

    protected $table = 'employees';
}
