<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonRelationManagers\RepositoriesRelationManager;
use App\Filament\Resources\CommonRelationManagers\ServersRelationManager;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Gitlab\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()
                    ->description(fn(Model $r) => $r->comment)
                    ->label('Название'),
                Tables\Columns\TextColumn::make('creds_url')
                    ->searchable()->sortable()
                    ->url(fn(Model $r): string|null => $r->creds_url, true)
                    ->getStateUsing(fn() => 'Перейти')
                    ->label('Доступы'),
                Tables\Columns\SelectColumn::make('status')
                    ->selectablePlaceholder(false)
                    ->options(Project::$statuses)
                    ->disabled(fn() => !auth()->user()->can('update_project'))
                    ->sortable()->label('Статус'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->createdBy->name)
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->updatedBy->name)
                    ->label('Обновлено'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Project::$statuses)->label('Статус'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Создано от'),
                        DatePicker::make('created_until')->label('Создано до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Filter::make('updated_at')
                    ->form([
                        DatePicker::make('created_from')->label('Обновлено от'),
                        DatePicker::make('created_until')->label('Обновлено до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\ProjectsOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
            Forms\Components\ToggleButtons::make('status')->options(Project::$statuses)->inline()->required()->label('Статус'),
            Forms\Components\TextInput::make('crm_id')->label('ID в CRM'),
            Forms\Components\TextInput::make('crm_url')
                ->suffixAction(
                    Action::make('goto')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->hidden(fn(Project $r) => !$r->exists)
                        ->url(fn(Project $r) => $r->crm_url, true)
                        ->label('Перейти к проекту')
                )
                ->suffixAction(
                    Action::make('open_collab')
                        ->icon('heroicon-o-arrow-up-right')
                        ->iconButton()
                        ->hidden(fn(Project $r) => !env('COLLAB_HOST') || $r->crm_url)
                        ->url(fn() => env('COLLAB_HOST'), true)
                        ->label('Открыть ActiveCollab')
                )
                ->required()->maxLength(255)->url()->label('Ссылка на CRM'),
            Forms\Components\TextInput::make('creds_url')
                ->suffixAction(
                    Action::make('goto_creds')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->hidden(fn(Project $r) => !$r->exists || !$r->creds_url)
                        ->url(fn(Project $r) => $r->creds_url, true)
                        ->label('Перейти к доступам')
                )
                ->suffixAction(
                    Action::make('open_vault')
                        ->icon('heroicon-o-arrow-up-right')
                        ->iconButton()
                        ->hidden(fn(Project $r) => !env('VAULT_HOST') || $r->creds_url)
                        ->url(fn() => env('VAULT_HOST'), true)
                        ->label('Открыть vault')
                )->maxLength(255)->url()->label('Доступы'),
            Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
        ];
    }
}
