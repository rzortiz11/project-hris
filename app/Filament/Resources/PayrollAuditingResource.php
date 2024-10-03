<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollAuditingResource\Pages;
use App\Filament\Resources\PayrollAuditingResource\Pages\ViewPayPeriodToAudit;
use App\Filament\Resources\PayrollAuditingResource\RelationManagers;
use App\Models\Employee;
use App\Models\PayPeriod;
use App\Models\Payroll;
use App\Models\PayrollAuditing;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;

class PayrollAuditingResource extends Resource
{
    protected static ?string $model = PayPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-check';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Payroll Auditing';

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
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPayrollAuditings::route('/'),
            'edit' => ViewPayPeriodToAudit::route('/{record}/pay-period/to-audit'),
        ];
    }
}
