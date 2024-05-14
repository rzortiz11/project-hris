<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'leave_document_id';
    
    protected $fillable = ['leave_id','filename','type','path'];
}
