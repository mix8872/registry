<?php

namespace Modules\Finance\Filament\Clusters\Finance\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\FinanceEconomy;

use function Clue\StreamFilter\fun;

class RecalcEconomyAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'Пересчитать';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Пересчитать');
        $this->color('success');
        $this->icon('heroicon-c-calculator');
        $this->successNotificationTitle('Отправлено на пересчёт');

        $this->hidden(fn(Model $r) =>  !in_array($r->status, [FinanceEconomy::STATUS_DONE, FinanceEconomy::STATUS_ERROR]));

        $this->action(function (): void {
            $this->process(function (FinanceEconomy $r): void {
                $r->setStatus(FinanceEconomy::STATUS_RECALC);
            });

            $this->success();
        });
    }
}
