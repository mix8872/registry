<?php

namespace Modules\Finance\Filament\Clusters\Finance\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\FinanceEconomy;

use function Clue\StreamFilter\fun;

class RecalcEconomyBulkAction extends BulkAction
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


        $this->action(function (): void {
            $this->process(function (Collection $records): void {
                foreach ($records as $r) {
                    if (!in_array($r->status, [FinanceEconomy::STATUS_DONE, FinanceEconomy::STATUS_ERROR])){
                        continue;
                    }
                    $r->setStatus(FinanceEconomy::STATUS_RECALC);
                }
            });

            $this->success();
        });
    }
}
