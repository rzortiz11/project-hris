<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContactAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_contact_address_id';
    
    protected $fillable = ['employee_id','region_id','city_id','district_id','barangay_id','landmark', 'unit_no', 'bldg_floor','street', 'subdivision','type'];
}
