<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityRegion extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'utility_region_id';
    protected $fillable = ['name', 'code'];

    function cities(): HasMany
    {
        return $this->hasMany(UtilityCity::class, 'utility_region_id');
    }
}
