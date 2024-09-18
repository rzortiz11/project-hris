<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Livewire\PayrollFormTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create Pay Period')
            ->icon('heroicon-s-document-text')
            ->iconPosition(IconPosition::Before)
            ->mutateFormDataUsing(function (array $data): array {
                $data['created_by'] = auth()->id();
         
                return $data;
            })
            ->form([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Select::make('type')->label('Type')
                    ->required()
                    ->options([
                        'weekly' => 'Weekly',
                        'biweekly' => 'Bi-Weekly',
                        'monthly' => 'Monthly'
                    ]),
                    Section::make('Pay Period Date')
                    ->schema([
                        DatePicker::make('start_date')
                        ->required()
                        ->label('Start Date')
                        ->suffixIcon('heroicon-o-calendar-days'),
                        DatePicker::make('end_date')
                        ->required()
                        ->label('End Date')
                        ->suffixIcon('heroicon-o-calendar-days'),   
                    ])
                    ->columns(2),
                    DatePicker::make('cut_off_date')
                    ->required()
                    ->label('Cut Off Date')
                    ->suffixIcon('heroicon-o-calendar-days'),   
                ])
                ->columns(1),    
            ])->modalWidth('3xl')
        ];
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }
}
