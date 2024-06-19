<?php

namespace App\Filament\Resources\CommonRelationManagers;

use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';
    protected static ?string $modelLabel = 'Проект';
    protected static ?string $pluralModelLabel = 'Проекты';
    protected static ?string $title = 'Проекты';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $icon = 'heroicon-s-squares-2x2';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('status')->options(Project::$statuses)->inline()->required()->label('Статус'),
                Forms\Components\TextInput::make('crm_id')->label('ID в CRM'),
                Forms\Components\TextInput::make('crm_url')->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Project $r) => $r->crm_url, true)
                )->required()->maxLength(255)->url()->label('Ссылка на CRM'),
                Forms\Components\TextInput::make('creds_url')->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Project $r) => $r->creds_url, true)
                )->maxLength(255)->url()->label('Доступы'),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                    ->description(fn(Project $r) => $r->comment)->label('Название'),
                Tables\Columns\SelectColumn::make('status')->selectablePlaceholder(false)
                    ->options(Project::$statuses)->sortable()->label('Статус'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Project::$statuses)->label('Статус'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->preloadRecordSelect()->color('primary'),
                Tables\Actions\CreateAction::make()->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
