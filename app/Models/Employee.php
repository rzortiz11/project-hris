<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_id';
    
    protected $fillable = ['employee_reference','user_id','title','gender','birthdate','religion', 'nationality', 'picture','is_active', 'progress','created_by'];
}
