<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\Pages\ViewEmployeePayroll;
use App\Filament\Resources\PayrollResource\Pages\ViewPayPeriod;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Employee;
use App\Models\PayPeriod;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollResource extends Resource
{
    protected static ?string $model = PayPeriod::class;

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Payroll Management';

    protected static ?string $modelLabel = "Employee Payrolls";

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pay_period_id')->label('ID'),
                TextColumn::make('type'),
                ColumnGroup::make('Pay Period Date', [
                    TextColumn::make('start_date'),
                    TextColumn::make('end_date'),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('cut_off_date')->searchable(),
                TextColumn::make('created_at')->label('Created Date')               
                ->getStateUsing(function (Employee $employee): string {
                    $created_at = Carbon::parse($employee->created_at);
                    return $created_at->format('Y-m-d H:i:s');
                })->searchable(),
                TextColumn::make('created_by')        
                ->getStateUsing(function (PayPeriod $record): string {

                    $user = User::find($record->created_by);
                    return $user ? ucwords(strtolower($user->name)) : '';
                }),       
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'edit' => ViewPayPeriod::route('/{record}/pay-period'),
            // 'view' => ViewEmployeePayroll::route('/{record}/employee-payrolls'),
            // 'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
