<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources;

use App\Filament\Clusters\Structure\Resources\ProjectResource;
use App\Livewire\AboutProject;
use App\Models\Project;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Filament\Clusters\Finance;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\RelationManagers;
use Modules\Finance\Models\FinanceEconomy;
use Modules\Finance\Models\FinanceRes;

class FinanceEconomiesResource extends Resource
{
    protected static ?string $model = FinanceEconomy::class;
    protected static ?string $navigationIcon = 'mdi-finance';
    protected static ?int $navigationSort = 1;
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $modelLabel = 'Отчет по проекту';
    protected static ?string $pluralModelLabel = 'Отчеты по проектам';
    protected static ?string $recordTitleAttribute = 'resource.name';
    protected static ?string $cluster = Finance::class;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields($form));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable()
                    ->searchable()
                    ->label('Проект'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->state(function (FinanceEconomy $r): string {
                        return FinanceEconomy::$statuses[$r->status];
                    })
                    ->label('Статус'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->createdBy->name ?? null)
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->updatedBy->name ?? null)
                    ->label('Обновлено'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('')->tooltip('Просмотр'),
                Tables\Actions\EditAction::make()->icon('mdi-pencil')->label('')->tooltip('Изменить'),
                Tables\Actions\CreateAction::make('clone')
                    ->label('')
                    ->color('info')
                    ->tooltip('Клонировать')
                    ->modalHeading(fn(FinanceEconomy $r) => "Клонировать в новый расчет экономики проекта")
                    ->icon('mdi-content-copy')
                    ->form(fn(Form $form) => self::form($form->model(FinanceEconomy::class)))
                    ->fillForm(fn(FinanceEconomy $r) => [
                        'rates' => $r->rates,
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SpentsRelationManager::class,
            RelationManagers\FactsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinanceEconomies::route('/'),
//            'create' => Pages\CreateFinanceEconomies::route('/create'),
            'view' => Pages\ViewFinanceEconomies::route('/{record}/view'),
            'edit' => Pages\EditFinanceEconomies::route('/{record}/edit'),
        ];
    }

    public static function getFormFields(Form|null $form = null): array
    {
        $fields = [];
        $resources = FinanceRes::get();
        foreach ($resources as $resource) {
            $fields[] = Forms\Components\Section::make($resource->name)
                ->schema([
                    Forms\Components\TextInput::make("rates.{$resource->name}.sold")
                        ->numeric()
                        ->placeholder(0)
                        ->default(0)
                        ->required()
                        ->extraInputAttributes(['onClick' => 'this.select()'])
                        ->label("Продано часов"),
                    Forms\Components\TextInput::make("rates.{$resource->name}.in")
                        ->numeric()
                        ->placeholder($resource->cost_in)
                        ->default($resource->cost_in)
                        ->required()
                        ->extraInputAttributes(['onClick' => 'this.select()'])
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, string|null $state) use ($resource) {
                            $component->state($state ?? $resource->cost_in);
                        })
                        ->label("Стоимость внутренняя"),
                    Forms\Components\TextInput::make("rates.{$resource->name}.out")
                        ->numeric()
                        ->placeholder($resource->cost_out)
                        ->default($resource->cost_out)
                        ->required()
                        ->extraInputAttributes(['onClick' => 'this.select()'])
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, string|null $state) use ($resource) {
                            $component->state($state ?? $resource->cost_out);
                        })
                        ->label("Стоимость внешняя"),
                    Forms\Components\Hidden::make("rates.{$resource->name}.id")
                        ->default($resource->id)
                        ->afterStateHydrated(function (Forms\Components\Hidden $component, string|null $state) use ($resource) {
                            $component->state($state ?? $resource->id);
                        })
                ])
                ->columns(3)
                ->compact();
        }

        return [
            Forms\Components\Select::make('project_id')
                ->relationship('project', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->unique(ignoreRecord: true)
                ->columnSpanFull()
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(FinanceEconomy $r) => $r->project_id ? ProjectResource::getUrl('edit', ['record' => $r->project_id]) : null, true)
                )
                ->label('Проект'),
            Forms\Components\Section::make('О проекте')->schema([
                Forms\Components\Livewire::make(AboutProject::class, ['model' => is_string($form->model) ? null : $form->model->project]),
            ])->hidden(fn(FinanceEconomy $r) => !$r->exists)->collapsed(),
            Forms\Components\Section::make("Ресурсы")
                ->schema($fields)
                ->collapsed()
                ->compact(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
