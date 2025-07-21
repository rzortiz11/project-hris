<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRequestApprover extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'request_approver_id';
    
    protected $fillable = ['employee_id','approver_id'];
}
