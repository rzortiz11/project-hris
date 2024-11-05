<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceManagement extends Attendance
{
    use HasFactory;

    protected $table = 'attendances';
}
