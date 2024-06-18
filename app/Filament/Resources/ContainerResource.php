<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContainerResource\Pages;
use App\Filament\Resources\ContainerResource\RelationManagers;
use App\Models\Container;
use App\Models\Repository;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static ?string $navigationIcon = 'mdi-docker';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Контейнер';
    protected static ?string $pluralModelLabel = 'Контейнеры';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
                Forms\Components\Select::make('repository_id')
                    ->relationship(name: 'repository', titleAttribute: 'name')
                    ->required()->label('Репозиторий')
                    ->suffixAction(
                        Action::make('Перейти')
                            ->icon('heroicon-m-globe-alt')
                            ->iconButton()
                            ->url(fn(Container $r) => $r->repository_id ? "/registry/repositories/{$r->repository_id}/edit" : null, true)
                    ),
                Forms\Components\Select::make('server_id')
                    ->relationship(name: 'server', titleAttribute: 'name')
                    ->required()->label('Сервер')
                    ->suffixAction(
                        Action::make('Перейти')
                            ->icon('heroicon-m-globe-alt')
                            ->iconButton()
                            ->url(fn(Container $r) => $r->server_id ? "/registry/servers/{$r->server_id}/edit" : null, true)
                    ),
                Forms\Components\TextInput::make('compose_path')->required()->columnSpanFull()->maxLength(255),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->description(fn(Container $r) => $r->comment)->label('Название'),
                Tables\Columns\TextColumn::make('repository.name')
                    ->label('Репозиторий')
                    ->url(fn(Container $r): string|null => $r->repository ? "/registry/repositories/{$r->repository->id}/edit" : null, true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('server.name')
                    ->label('Сервер')
                    ->url(fn(Container $r): string|null => $r->server ? "/registry/servers/{$r->server->id}/edit" : null, true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('repository')
                    ->multiple()
                    ->relationship('repository', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Репозиторий'),
                SelectFilter::make('server')
                    ->multiple()
                    ->relationship('server', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Сервер')
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContainers::route('/'),
            'create' => Pages\CreateContainer::route('/create'),
            'edit' => Pages\EditContainer::route('/{record}/edit'),
        ];
    }
}
