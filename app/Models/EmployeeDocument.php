<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_document_id';
    
    protected $fillable = ['employee_id','document_type','document_remarks'];

    
    public function attachments(): HasMany {
        return $this->hasMany(Document::class, 'employee_document_id', 'employee_document_id');
    }
}
