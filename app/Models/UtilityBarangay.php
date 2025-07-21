<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityBarangay extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'utility_barangay_id';
    protected $fillable = ['name', 'code', 'utility_district_id'];
}
