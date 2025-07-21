<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticeBoard extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'notice_board_id';

    protected $fillable = ['title','description','publish_at','visible','active','attachments', 'created_by','employees_id'];

    protected $casts = [
        'attachments' => 'array',
        'employees_id' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function noticeEmployee(): HasMany
    {
        return $this->hasMany(NoticeEmployee::class, 'notice_board_id', 'notice_board_id');
    }
}
