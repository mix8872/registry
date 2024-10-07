<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Filament\Clusters\Finance;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource\Pages;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource\RelationManagers;
use Modules\Finance\Models\FinanceRes;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceResResource extends Resource
{
    protected static ?string $model = FinanceRes::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $modelLabel = 'Ресурс';
    protected static ?string $pluralModelLabel = 'Ресурсы';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $cluster = Finance::class;

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()
                    ->description(fn(Model $r) => $r->comment)
                    ->label('Название'),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => FinanceRes::$types[$state] ?? null)
                    ->label('Тип'),
                Tables\Columns\TextColumn::make('cost_in')
                    ->searchable()->sortable()
                    ->numeric()
                    ->label('Стоимость внутрення'),
                Tables\Columns\TextColumn::make('cost_out')
                    ->searchable()->sortable()
                    ->numeric()
                    ->label('Стоимость внешняя'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()->dateTime()
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()->dateTime()
                    ->label('Обновлено'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(FinanceRes::$types)->label('Тип'),
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
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->extraModalFooterActions(fn (Action $action): array => [
                        EditAction::make()->slideOver()->form(static::getFormFields()),
                    ]),
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResources::route('/'),
//            'view' => Pages\ViewResource::route('/{record}'),
//            'create' => Pages\CreateResource::route('/create'),
//            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
            Forms\Components\Select::make('type')->required()
                ->searchable()
                ->options(FinanceRes::$types)->label('Тип'),
            Forms\Components\TextInput::make('cost_in')->numeric()->label('Стоимость внутренняя'),
            Forms\Components\TextInput::make('cost_out')->numeric()->label('Стоимость внешняя'),
            Forms\Components\TextInput::make('comment')->columnSpanFull()->label('Примечание')
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
