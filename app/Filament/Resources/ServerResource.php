<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonRelationManagers\ContainersRelationManager;
use App\Filament\Resources\CommonRelationManagers\RepositoriesRelationManager;
use App\Filament\Resources\ServerResource\Pages;
use App\Filament\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    protected static ?string $modelLabel = 'Сервер';
    protected static ?string $pluralModelLabel = 'Серверы';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-s-server-stack';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
                Forms\Components\TextInput::make('creds_url')->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Server $r) => $r->creds_url, true)
                )->maxLength(255)->url()->required()->label('Доступы'),
                Forms\Components\Toggle::make('has_public_nat')->label('Сервер за NAT'),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Server $r) => $r->comment)->searchable()->sortable()->label('Название'),
                Tables\Columns\IconColumn::make('has_public_nat')->boolean()->sortable()->label('Сервер за NAT'),
                Tables\Columns\TextColumn::make('creds_url')->url(fn (Server $r) => $r->creds_url, true)->label('Доступы'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('has_public_nat'),
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
            RelationManagers\AddressesRelationManager::class,
            RepositoriesRelationManager::class,
            ContainersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
