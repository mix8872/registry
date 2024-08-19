<?php

namespace App\Filament\Resources\CommonRelationManagers;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
        return $form->schema(ProjectResource::getFormFields());
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
