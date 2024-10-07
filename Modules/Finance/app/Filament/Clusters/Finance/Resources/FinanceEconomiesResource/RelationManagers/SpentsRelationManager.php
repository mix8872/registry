<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Finance\Models\FinanceEconomySpent;
use Modules\Finance\Models\FinanceRes;

class SpentsRelationManager extends RelationManager
{
    protected static string $relationship = 'spents';
    protected static ?string $title = 'Итог';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Итог')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Итог')
            ->groups([
                Group::make('resource.type')
                    ->getTitleFromRecordUsing(fn(FinanceEconomySpent $record): string => FinanceRes::$types[$record->resource->type])
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('resource.type')
            ->columns([
                Tables\Columns\TextColumn::make('resource.name')->label('Ресурс'),
                Tables\Columns\TextColumn::make('rate_in')->money('RUB')->label('Цена внутренняя'),
                Tables\Columns\TextColumn::make('rate_out')->money('RUB')->label('Цена внешняя'),
                Tables\Columns\TextColumn::make('sold_count')->label('Часов продано'),
                Tables\Columns\TextColumn::make('spent_count')
                    ->state(function (FinanceEconomySpent $r): string {
                        return round($r->spent_count / 3600);
                    })
                    ->label('Часов потрачено'),
                Tables\Columns\TextColumn::make('relation')->numeric()->suffix('%')
                    ->label('Соотношение'),
                Tables\Columns\TextColumn::make('price_in')->money('RUB')
                    ->summarize(Average::make())
                    ->label('Стоимость внутренняя'),
                Tables\Columns\TextColumn::make('price_out')->money('RUB')
                    ->summarize(Average::make())
                    ->label('Стоимость внешняя'),
                Tables\Columns\TextColumn::make('performance')
                    ->summarize(Average::make())
                    ->label('Эффективность'),
                Tables\Columns\TextColumn::make('profit')
                    ->summarize(Average::make())
                    ->money('RUB')->label('Доход'),
            ]);
    }
}
