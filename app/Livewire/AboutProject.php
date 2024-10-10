<?php

namespace App\Livewire;

use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class AboutProject extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

//    protected $listeners = ['refreshComponent' => '$refresh'];

    public Model $model;

    public function mount($model): void
    {
        $this->model = is_string($model) ? new $model() : $model;
    }

    public function render()
    {
        return view('livewire.infolist');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->record($this->model)
            ->schema([
                Components\TextEntry::make('client')
                    ->label('Клиент'),
                Components\TextEntry::make('customer.name')
                    ->label('Заказчик'),
                Components\TextEntry::make('legal_customer')
                    ->label('Юрлицо заказчика'),
                Components\TextEntry::make('legal_inner')
                    ->label('Юрлицо внутреннее'),
                Components\TextEntry::make('contract_date')
                    ->label('Дата заключения'),
                Components\TextEntry::make('contract_close_date')
                    ->label('Дата сдачи'),
                Components\TextEntry::make('payment_type')
                    ->formatStateUsing(fn (string $state): string => Project::$payments[$state])
                    ->label('Тип оплаты'),
                Components\TextEntry::make('payment_period')
                    ->formatStateUsing(fn (string $state): string => Project::$paymentPeriods[$state])
                    ->label('Периодичность оплаты'),
                Components\TextEntry::make('workType.name')
                    ->label('Тип работы'),
                Components\TextEntry::make('contract')
                    ->label('Договор'),
                Components\TextEntry::make('cost')
                    ->numeric()
                    ->label('Стоимость')
            ])
            ->columns(4);
    }
}
