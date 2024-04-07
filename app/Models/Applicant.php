<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'applicant_id';
    protected $fillable = ['first_name','last_name','suffix','middle_name','mobile','email', 'status','password',];

    /** Modify name attribute  */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
