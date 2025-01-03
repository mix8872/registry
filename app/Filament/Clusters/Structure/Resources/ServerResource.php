<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\ContainersRelationManager;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\ProjectsRelationManager;
use App\Filament\Clusters\Structure\Resources\ServerResource\Pages;
use App\Filament\Clusters\Structure\Resources\ServerResource\RelationManagers;
use App\Filament\Clusters\Structure\Resources\ServerResource\RelationManagers\RepositoriesRelationManager;
use App\Livewire\ListAddresses;
use App\Models\Server;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    protected static ?string $modelLabel = 'Сервер';
    protected static ?string $pluralModelLabel = 'Серверы';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-s-server-stack';
    protected static ?int $navigationSort = 3;
    protected static ?string $cluster = Structure::class;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields($form));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Model $r) => $r->comment)
                    ->wrap()
                    ->searchable()->sortable()
                    ->label('Название'),
                Tables\Columns\IconColumn::make('is_public_nat')
                    ->boolean()->sortable()
                    ->label('Сервер за NAT'),
                Tables\Columns\TextColumn::make('creds_url')
                    ->url(fn(Model $r) => $r->creds_url, true)
                    ->getStateUsing(fn() => 'Перейти')
                    ->label('Доступы'),
                Tables\Columns\TextColumn::make('last_updated_at')
                    ->sortable()->date()
                    ->badge()
                    ->color(function (Model $r) {
                        $d = Carbon::createFromDate($r->last_updated_at);
                        $diff = $d->diffInMonths(Carbon::now());

                        return match (true) {
                            $diff > 3 => 'danger',
                            $diff > 2 => 'warning',
                            default => 'success'
                        };
                    })
                    ->label('Последнее обновление'),
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn(Model $r) => $r->createdBy->name)
                    ->sortable()->dateTime()
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->description(fn(Model $r) => $r->updatedBy->name)
                    ->sortable()->dateTime()
                    ->label('Обновлено'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public_nat')->label('Сервер за NAT'),
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
                Filter::make('last_updated_at')
                    ->form([
                        DatePicker::make('created_from')->label('Обновление от'),
                        DatePicker::make('created_until')->label('Обновление до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('last_updated_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('last_updated_at', '<=', $date),
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
            ->defaultSort('updated_at', 'desc')
            ->poll('5s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AddressesRelationManager::class,
            RepositoriesRelationManager::class,
            ContainersRelationManager::class,
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit')
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getFormFields($form)
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
            Forms\Components\TextInput::make('creds_url')
                ->maxLength(255)->url()->required()
                ->suffixAction(
                    Action::make('goto_creds')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->hidden(fn(Server $r) => !$r->exists || !$r->creds_url)
                        ->url(fn(Server $r) => $r->creds_url, true)
                        ->label('Перейти к доступам')
                )
                ->suffixAction(
                    Action::make('open_vault')
                        ->icon('heroicon-o-arrow-up-right')
                        ->iconButton()
                        ->hidden(fn(Server $r) => config('services.vault.host') || $r->creds_url)
                        ->url(fn(Server $r) => onfig('services.vault.host'), true)
                        ->label('Открыть vault')
                )
                ->label('Доступы'),
            Forms\Components\Toggle::make('is_public_nat')->label('Сервер за NAT'),
            Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            Forms\Components\DatePicker::make('last_updated_at')->label('Дата последнего обновления'),
            Forms\Components\CheckboxList::make('checklist')
                ->options(Server::$checklistOptions)
                ->columns(2)
//                ->columnSpanFull()
                ->label('Чеклист'),
        ];
    }
}
