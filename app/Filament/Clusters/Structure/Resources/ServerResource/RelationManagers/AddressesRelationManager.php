<?php

namespace App\Filament\Clusters\Structure\Resources\ServerResource\RelationManagers;

use App\Models\IpAddress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    protected static ?string $title = 'IP адреса';
    protected static ?string $modelLabel = 'IP адрес';
    protected static ?string $pluralModelLabel = 'IP адреса';
    protected static ?string $icon = 'heroicon-o-computer-desktop';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip_addr')
                    ->ip()
                    ->required()
                    ->maxLength(255)->label('IP адрес')
                    ->validationMessages([
                        'ip' => ':attribute заполнен неверно',
                    ]),
                Forms\Components\Toggle::make('is_public')->label('Белый IP'),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ip_addr')
            ->columns([
                Tables\Columns\TextColumn::make('ip_addr')->description(fn (IpAddress $r) => $r->comment)->label('Адрес'),
                Tables\Columns\IconColumn::make('is_public')->boolean()->label('Белый IP'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon('mdi-pencil'),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
