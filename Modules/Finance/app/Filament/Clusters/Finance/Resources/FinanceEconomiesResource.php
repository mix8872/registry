<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources;

use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Filament\Clusters\Finance;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\RelationManagers;
use Modules\Finance\Liveware\ListSpentData;
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
                    ->label('Проект')
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
            ]);
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
                        ->default(0)
                        ->required()
                        ->label("Продано часов"),
                    Forms\Components\TextInput::make("rates.{$resource->name}.in")
                        ->numeric()
                        ->default($resource->cost_in)
                        ->required()
                        ->label("Стоимость внутренняя"),
                    Forms\Components\TextInput::make("rates.{$resource->name}.out")
                        ->numeric()
                        ->default($resource->cost_out)
                        ->required()
                        ->label("Стоимость внешняя"),
                    Forms\Components\Hidden::make("rates.{$resource->name}.id")
                        ->default($resource->id)
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
                ->columnSpanFull()
                ->label('Проект'),
            Forms\Components\Section::make("Ресурсы")
                ->schema($fields)
                ->collapsed()
                ->compact(),
            /*Forms\Components\Livewire::make(ListSpentData::class, ['model' => $form->model])
                ->disabled(function (FinanceEconomy $r) {
                    return !$r->exists();
                })
                ->hidden(function (FinanceEconomy $r) {
                    return !$r->exists();
                })
                ->columnSpanFull()
                ->label('Итог'),*/
        ];
    }
}
