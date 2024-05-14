<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'leave_id';
    
    protected $fillable = ['employee_id','job_position','type','date_filling','from','to', 'hours', 'remarks', 'approver_id','action_date','disapproved_reason','is_paid','status'];

    public function leave_documents(): HasMany {
        return $this->hasMany(LeaveDocument::class, 'leave_id', 'leave_id');
    }
}
