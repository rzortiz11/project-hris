<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payroll_id';
    
    protected $fillable = [
        'pay_period_id','employee_id','fullname','job_position','reporting_designation','location','company', 'cut_off','cut_off_from','cut_off_to','day_range','working_days',
        'regular_overtime_hours','rest_day_hours','rest_day_overtime_hours','legal_holiday_hours','legal_holiday_overtime_hours','special_holiday_hours', 'special_holiday_overtime_hours',
        'absent','late_days','late_hours','leave_days', 'leave_hours','time_change_hours','over_time_hours','under_time_hours' ,'retro_hours',
        'basic_pay','over_time_pay','holiday_pay','allowances_pay','retro_pay','bonuses_pay',
        'total_gross_pay','sss_contribution','philhealth_contribution','pagibig_contribution', 'other_deductions','taxable_income','income_tax_withheld','total_net_pay',
        'status','created_by','updated_by', 'cash_advance', 'adjustment', 'remarks', 'is_viewable'
    ];

    // is over_time_hours == regular_overtim_hours
    // or regular_overtime_hours + $rest_day_hours == over_time_hours and over_time_pay

    
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pay_period_id', 'pay_period_id');
    }

    public function pay_period(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class, 'pay_period_id', 'pay_period_id');
    }

    public function allowance(): HasMany
    {
        return $this->hasMany(PayrollAllowance::class, 'payroll_id', 'payroll_id');
    }
    
    public function deduction(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'payroll_id', 'payroll_id');
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(PayrollBonus::class, 'payroll_id', 'payroll_id');
    }

    public function contribution(): HasMany
    {
        return $this->hasMany(PayrollEmployeeContribution::class, 'payroll_id', 'payroll_id');
    }
}
