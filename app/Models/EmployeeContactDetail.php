<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContactDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_contact_id';
    
    protected $fillable = ['employee_id','mobile','secondary_mobile','email','secondary_email','telephone', 'secondary_telephone', 'facebook_profile','linkedIn_profile'];
}
