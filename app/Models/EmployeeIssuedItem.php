<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeIssuedItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_issued_item_id';
    
    protected $fillable = ['employee_id','item_type','item_name', 'item_model', 'issued_date'];
}
