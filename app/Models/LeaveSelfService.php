<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSelfService extends Leave
{
    use HasFactory;

    protected $table = 'leaves';
}
