<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Payroll;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ViewEmployeePayrollTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        $pay_period_id = $this->record->pay_period_id;
        
        return $table
            ->query(Payroll::query()->where('pay_period_id', $pay_period_id))
            // ->defaultGroup('status')
            ->columns([
                ColumnGroup::make('Employee Details', [
                    TextColumn::make('payroll_id')->label('ID'),
                    ImageColumn::make('avatar')
                    ->grow(false)
                    ->getStateUsing(function ($record): string {
                        $employee = Employee::find($record->employee_id);
                        return isset($employee->picture) ? $employee->picture : '';
                    })
                    ->circular(),
                    TextColumn::make('fullname')->label('Employee Name')->searchable(),
                    TextColumn::make('job_position')->label('Position'),
                    TextColumn::make('reporting_designation')->label('Designation'),
                    TextColumn::make('location')->label('Location'),
                    TextColumn::make('company')->label('Company'),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),

                // TextColumn::make('total_gross_pay')->label('Gross Pay'),
                TextColumn::make('basic_pay')->label('Basic Pay/cutoff')->prefix('₱')->alignEnd(),
                TextColumn::make('total_gross_pay')->label('Total Gross Pay')->prefix('₱')->weight(FontWeight::Bold)->alignEnd()
                ->extraAttributes(function ($state) {
                    $bgColor = '#d3d3d3';
                    return ['style' => "background-color: {$bgColor}"];
                }),
                ColumnGroup::make('Mandatory Contributions', [
                    TextColumn::make('sss_contribution')->label('SSS')->alignEnd(),
                    TextColumn::make('pagibig_contribution')->label('Pag-Ibig')->alignEnd(),
                    TextColumn::make('philhealth_contribution')->label('PhilHealth')->alignEnd(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('other_deductions')->label('Other Deductions')->alignEnd(),
                ColumnGroup::make('Income', [
                    TextColumn::make('taxable_income')->label('Taxable')->alignEnd(),
                    TextColumn::make('income_tax_withheld')->label('Tax with Held')->alignEnd(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('cash_advance')->label('Cash Advance')->alignEnd(),
                TextColumn::make('adjustment')->label('Adjsutment')->alignEnd(),
                TextColumn::make('total_net_pay')->label('Total Net Pay')->prefix('₱')->weight(FontWeight::Bold)->alignEnd()
                ->summarize(Sum::make()->label('Total')->numeric()->money('PHP'))
                ->extraAttributes(function ($state) {
                    $bgColor = '#d3d3d3';
                    return ['style' => "background-color: {$bgColor}"];
                }),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'denied' => 'danger',
                    'void' => 'danger',
                })
                ->label('Status'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Approve')
                ->color('success')
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(function (Payroll $record, array $data) {
                    $record->status = "approved";
                    $record->save();
                })->requiresConfirmation(),
                Tables\Actions\Action::make('pdf')
                ->label('Payslip')
                ->color('danger')
                ->icon('heroicon-s-document-arrow-down')
                ->action(function (Payroll $record, array $data) {

                    redirect()->route('download.payslip.pdf', ['payroll_id' => $record->payroll_id]);
                }),
                // ->requiresConfirmation(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    // Tables\Actions\EditAction::make(), payslip should not be editable either create or delete only
                    Tables\Actions\DeleteAction::make(),
                ]),
         
            ])
            ->striped()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Approved')
                    ->color('success')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->requiresConfirmation()
                    ->action(function (Collection $records){
                        foreach ($records as $record) {
                            dump($record);
                        }
                        dump($records);
                    }),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.view-employee-payroll-table');
    }
}
