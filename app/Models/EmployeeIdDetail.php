<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeIdDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_id_detail_id';
    
    protected $fillable = ['employee_id','employee_number','sss_number','pagibig_number', 'philhealth_number', 'tin_number'];
}
