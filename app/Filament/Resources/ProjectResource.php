<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonRelationManagers\RepositoriesRelationManager;
use App\Filament\Resources\CommonRelationManagers\ServersRelationManager;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ViewField;

//use Filament\Resources\Tables\Columns;
//use Filament\Resources\Tables\Filter;
//use Filament\Resources\Tables\Table as TableRes;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $modelLabel = 'Проект';
    protected static ?string $pluralModelLabel = 'Проекты';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-s-squares-2x2';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RepositoriesRelationManager::class,
            ServersRelationManager::class,
            RelationManagers\StacksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
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
        ];
    }
}
