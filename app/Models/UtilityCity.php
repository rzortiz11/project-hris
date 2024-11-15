<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityCity extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'utility_city_id';
    protected $fillable = ['name', 'code', 'utility_region_id'];

    function districts(): HasMany
    {
        return $this->hasMany(UtilityDisctrict::class, 'utility_city_id');
    }
}
