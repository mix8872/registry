<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\FinanceEconomy;
use Modules\Finance\Models\FinanceRes;

class FactsRelationManager extends RelationManager
{
    protected static string $relationship = 'facts';
    protected static ?string $title = 'Затраты времени';
    protected static ?string $modelLabel = 'Затраты времени';
    protected static ?string $pluralModelLabel = 'Затраты времени';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('finance_res_id')
                ->relationship('resource', 'name')
                ->required()
                ->label('Ресурс'),
            Forms\Components\Hidden::make('project_id')->default(fn (FinanceEconomy $r) => $r->project_id),
            Forms\Components\DatePicker::make('date')
                ->required()
                ->label('Дата'),
            Forms\Components\TimePicker::make('count')
                ->required()
                ->seconds(false)
                ->label('Затраты'),
            Forms\Components\TextInput::make('comment')
                ->columnSpanFull()
                ->label('Комментарий')

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('resource.name')->sortable()
                    ->label('Ресурс'),
                Tables\Columns\TextColumn::make('date')->sortable()
                    ->date()->label('Дата'),
                Tables\Columns\TextColumn::make('count')
                    ->description(fn(Model $r) => $r->comment)->sortable()
                    ->time('H:i')
                    ->summarize([
                        Average::make()->formatStateUsing(fn($state) => date('H:i', $state)),
                        Sum::make()->formatStateUsing(fn($state) => date('H:i', $state)),
                        Range::make()->formatStateUsing(fn($state) => [date('H:i', $state[0]), date('H:i', $state[1])])
                            ->label('Мин.\Макс.'),
                    ])
                    ->label('Затраты'),
                Tables\Columns\TextColumn::make('created_at')->sortable()->dateTime()->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()->label('Обновлено'),
                Tables\Columns\ToggleColumn::make('crm_id')->disabled()->sortable()->label('Из ActiveСollab')
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('crm_id')->queries(
                    true: fn (Builder $query) => $query->whereNotNull('crm_id'),
                    false: fn (Builder $query) => $query->whereNull('crm_id'),
                    blank: fn (Builder $query) => $query,
                )->label('Из ActiveCollab'),
                Tables\Filters\SelectFilter::make('finance_res_id')
                    ->options(FinanceRes::get()->pluck('name', 'id'))
                    ->multiple()
                    ->label('Ресурс'),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from')->label('Дата от'),
                        DatePicker::make('date_until')->label('Дата до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->extraModalFooterActions(fn(Action $action): array => [
                        EditAction::make()->slideOver()->form(FinanceSpentFactResource::getFormFields())
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
