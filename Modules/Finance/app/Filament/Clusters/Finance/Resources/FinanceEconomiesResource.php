<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources;

use Modules\Finance\Filament\Clusters\Finance;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\RelationManagers;
use Modules\Finance\Models\FinanceEconomy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                    ->label('Статус')
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
//                ->collapsed()
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
                ->label('Проект'),
            Forms\Components\Section::make("Ресурсы")
                ->schema($fields)
                ->collapsed()
                ->compact(),
        ];
    }
}
