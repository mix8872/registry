<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\RepositoriesRelationManager;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\ServersRelationManager;
use App\Filament\Clusters\Structure\Resources\ProjectResource\Pages;
use App\Filament\Clusters\Structure\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $modelLabel = 'Проект';
    protected static ?string $pluralModelLabel = 'Проекты';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-s-squares-2x2';
    protected static ?int $navigationSort = 1;
    protected static ?string $cluster = Structure::class;

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
                    ->wrap()
                    ->label('Название'),
                Tables\Columns\TextColumn::make('creds_url')
                    ->searchable()->sortable()
                    ->url(fn(Model $r): string|null => $r->creds_url, true)
                    ->getStateUsing(fn(Model $r) => $r->creds_url ? 'Перейти' : null)
                    ->label('Доступы'),
                Tables\Columns\SelectColumn::make('status')
                    ->selectablePlaceholder(false)
                    ->options(Project::$statuses)
                    ->disabled(fn() => !auth()->user()->can('update_project'))
                    ->sortable()->label('Статус'),
                Tables\Columns\TextColumn::make('contract_date')
                    ->sortable()->dateTime()
                    ->label('Дата заключения'),
                Tables\Columns\TextColumn::make('contract_close_date')
                    ->sortable()->dateTime()
                    ->label('Дата сдачи'),
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
                Tables\Actions\ViewAction::make()->size(ActionSize::ExtraLarge)->label(''),
                Tables\Actions\EditAction::make()->icon('mdi-pencil')->size(ActionSize::ExtraLarge)->label(''),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick')->requiresConfirmation()->size(ActionSize::ExtraLarge)->label(''),
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
                        ->hidden(fn(Project $r) => !config('services.collab.host') || $r->crm_url)
                        ->url(fn() => config('services.collab.host'), true)
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
                        ->hidden(fn(Project $r) => !config('services.vault.host') || $r->creds_url)
                        ->url(fn() => config('services.vault.host'), true)
                        ->label('Открыть vault')
                )->maxLength(255)->url()->label('Доступы'),
            Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            Forms\Components\TextInput::make('client')->label('Клиент'),
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->required()
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->required()->label('Название'),
                ])
                ->createOptionModalHeading('Новый заказчик')
                ->label('Заказчик'),
            Forms\Components\TextInput::make('legal_customer')->label('Юрлицо заказчика'),
            Forms\Components\Select::make('legal_inner')
                ->options(Project::$legals)
                ->required()
                ->label('Юрлицо внутреннее'),
            Forms\Components\DatePicker::make('contract_date')->label('Дата заключения'),
            Forms\Components\DatePicker::make('contract_close_date')->label('Дата сдачи'),
            Forms\Components\Select::make('payment_type')
                ->options(Project::$payments)
                ->required()
                ->label('Тип оплаты'),
            Forms\Components\Select::make('payment_period')
                ->options(Project::$paymentPeriods)
                ->required()
                ->label('Периодичность оплаты'),
            Forms\Components\Select::make('work_type_id')
                ->relationship('workType', 'name')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->required()->label('Название'),
                ])
                ->createOptionModalHeading('Новый тип работы')
                ->required()
                ->searchable()
                ->label('Тип работы'),
            Forms\Components\TextInput::make('contract')->label('Договор'),
            Forms\Components\TextInput::make('cost')->numeric()->label('Стоимость')
        ];
    }
}
