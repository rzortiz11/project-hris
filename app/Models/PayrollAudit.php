<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'audit_id';
    
    protected $fillable = [
        'payroll_id','audited_by','status','remarks'
    ];
}
