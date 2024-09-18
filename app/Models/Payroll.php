<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payroll_id';
    
    protected $fillable = [
        'pay_period_id','employee_id','fullname','job_position','reporting_designation','location','company', 'cut_off','cut_off_from','cut_off_to','day_range','working_days',
        'regular_overtime_hours','rest_day_hours','rest_day_overtime_hours','legal_holiday_hours','legal_holiday_overtime_hours','special_holiday_hours', 'special_holiday_overtime_hours',
        'absent','late_days','late_hours','leave_days', 'leave_hours','time_change_hours','over_time_hours','under_time_hours' ,'retro_hours',
        'basic_pay','time_change_pay','over_time_pay','holiday_pay', 'under_time_pay','allowances_pay','retro_pay','bonuses_pay',
        'total_gross_pay','sss_contribution','philhealth_contribution','pagibig_contribution', 'other_deductions','taxable_income','income_tax_withheld','total_net_pay',
        'status','created_by','updated_by', 'cash_advance', 'adjustment'
    ];

    // is over_time_hours == regular_overtim_hours
    // or regular_overtime_hours + $rest_day_hours == over_time_hours and over_time_pay

}
