<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityDisctrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'utility_district_id';
    protected $fillable = ['name', 'code', 'utility_city_id'];

    function barangays(): HasMany
    {
        return $this->hasMany(UtilityBarangay::class, 'utility_district_id');
    }
}
