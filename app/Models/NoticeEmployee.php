<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'notice_employee_id';

    protected $fillable = ['notice_board_id','employee_id'];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'user_id');
    }

    public function notice_board(): BelongsTo
    {
        return $this->belongsTo(NoticeBoard::class, 'notice_board_id', 'notice_board_id');
    }
}
