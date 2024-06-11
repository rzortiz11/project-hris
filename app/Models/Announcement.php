<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'announcement_id';

    protected $fillable = ['title','description','publish_at','expires_at','visible','active','attachments'];

    protected $casts = [
        'attachments' => 'array',
    ];
}
