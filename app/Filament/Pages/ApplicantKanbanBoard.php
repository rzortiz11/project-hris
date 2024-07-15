<?php

/*
namespace App\Filament\Pages;

use App\ApplicationStatusTypeEnum;
use App\Models\Applicant;
use App\Models\ApplicationStatusType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section as FormSection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class ApplicantKanbanBoard extends KanbanBoard
{
    protected static string $model = Applicant::class;
    protected static string $statusEnum = ApplicationStatusTypeEnum::class;

    protected static ?string $navigationGroup = 'Recruitment Management';
    
    protected static ?string $navigationLabel = 'Applicant Board';

    protected static string $recordTitleAttribute = 'name';
    protected static string $recordStatusAttribute = 'status';

    protected static string $view = 'filament-kanban::applicant-kanban-board';

    protected string $editModalWidth = '6xl';

    protected function records(): Collection
    {
        return Applicant::all();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->model(Applicant::class)
            ->form([
                Wizard::make([
                    Wizard\Step::make('Application Details')
                        ->icon('heroicon-m-user')
                        ->schema([
                            TextInput::make('first_name')->required(),
                            TextInput::make('last_name')->required(),
                            TextInput::make('middle_name'),
                            TextInput::make('suffix'),
                            TextInput::make('mobile')->required(),
                            TextInput::make('email')->required(),        
                            Forms\Components\Select::make('assigned_to')->label('Assigned To')
                            ->required()
                            ->options(User::all()->pluck('name','user_id')->map(function ($name) {
                                return ucwords(strtolower($name));
                            })->toArray())
                            ->searchable()
                            ->preload(),
                            Forms\Components\Placeholder::make('created_by')
                            ->label('Created By')
                        ])
                        ->columns(4),
                    Wizard\Step::make('Employment History')
                        ->schema([
                        ]),
                    Wizard\Step::make('Job Details')
                    ->schema([
                    ]),
                    Wizard\Step::make('Salary Expectation')
                        ->schema([
                        ]),
                    Wizard\Step::make('Remarks')
                    ->label('Remarks')
                    ->schema([
                    ]),                        
                ])   
            ])->modalWidth('6xl'),
        ];
    }

    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('Application Details')
                    ->icon('heroicon-m-user')
                    ->schema([
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('middle_name'),
                        TextInput::make('suffix'),
                        TextInput::make('mobile')->required(),
                        TextInput::make('email')->required(),
                        Forms\Components\Select::make('assigned_to')->label('Assigned To')
                        ->required()
                        ->options(User::all()->pluck('name','user_id')->map(function ($name) {
                            return ucwords(strtolower($name));
                        })->toArray())
                        ->searchable()
                        ->preload(),
                        Forms\Components\Placeholder::make('created_by')
                        ->label('Created By')
                        ->content(fn (?Applicant $record): ?string => isset($record->user->name) ? $record->user->name : ""),             
                    ])
                    ->columns(4),
                Wizard\Step::make('Employment History')
                    ->schema([
                    ]),
                Wizard\Step::make('Job Details')
                ->schema([
                ]),
                Wizard\Step::make('Salary Expectation')
                    ->schema([
                    ]),
                Wizard\Step::make('Remarks')
                ->schema([
                ])
            ])->skippable(),          
      
        ];
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        $applicant = Applicant::find($recordId);
        $applicant->status = $status;
        $applicant->save();
    }
}
*/
?>