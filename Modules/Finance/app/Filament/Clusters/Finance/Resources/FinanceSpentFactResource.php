<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Filament\Clusters\Finance;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource\Pages;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource\RelationManagers;
use Modules\Finance\Models\FinanceRes;
use Modules\Finance\Models\FinanceSpentFact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceSpentFactResource extends Resource
{
    protected static ?string $model = FinanceSpentFact::class;
    protected static ?string $navigationIcon = 'mdi-clipboard-text-clock';
    protected static ?int $navigationSort = 2;
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $modelLabel = 'Затраты времени';
    protected static ?string $pluralModelLabel = 'Затраты времени';
    protected static ?string $recordTitleAttribute = 'resource.name';
    protected static ?string $cluster = Finance::class;

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('resource.name')
                    ->label('Ресурс'),
                Tables\Columns\TextColumn::make('project.name')
                    ->description(fn(Model $r) => $r->task_url)
                    ->wrap()
                    ->url(fn(Model $r) => $r->task_url, true)
                    ->label('Проект'),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date()
                    ->label('Дата'),
                Tables\Columns\TextColumn::make('count')
                    ->description(fn(Model $r) => $r->comment)
                    ->wrap()
                    ->time('H:i')
                    ->summarize([
                        Average::make()->formatStateUsing(fn($state) => date('H:i', $state)),
                        Sum::make()->formatStateUsing(fn($state) => date('H:i', $state)),
                        Range::make()->formatStateUsing(fn($state) => [date('H:i', $state[0]), date('H:i', $state[1])])
                            ->label('Мин.\Макс.'),
                    ])
                    ->label('Затраты')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('finance_res_id')
                    ->options(FinanceRes::get()->pluck('name', 'id'))
                    ->multiple()
                    ->label('Ресурс'),
                Tables\Filters\SelectFilter::make('project_id')
                    ->options(Project::get()->pluck('name', 'id'))
                    ->multiple()
                    ->label('Проект'),
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
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->size(ActionSize::ExtraLarge)->label('')
                    ->extraModalFooterActions(fn(Action $action): array => [
                        EditAction::make()->slideOver()->form(static::getFormFields())
                    ]),
                Tables\Actions\EditAction::make()->icon('mdi-pencil')
                    ->visible(fn(Model $r) => empty($r->crm_id))->requiresConfirmation()
                    ->slideOver()
                ->size(ActionSize::ExtraLarge)->label(''),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick')
                    ->visible(fn(Model $r) => empty($r->crm_id))->requiresConfirmation()
                ->size(ActionSize::ExtraLarge)->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinanceSpentFacts::route('/'),
//            'create' => Pages\CreateFinanceSpentFact::route('/create'),
//            'edit' => Pages\EditFinanceSpentFact::route('/{record}/edit'),
        ];
    }

    public static function getFormFields(): array
    {
        return [
            Forms\Components\Select::make('finance_res_id')
                ->relationship('resource', 'name')
                ->required()
                ->label('Ресурс'),
            Forms\Components\Select::make('project_id')
                ->relationship('project', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Проект'),
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

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
