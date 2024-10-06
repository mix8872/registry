<?php

namespace Modules\Finance\Console\Commands;

use App\Classes\ActiveCollabClient;
use Modules\Finance\Models\FinanceRes;
use Illuminate\Console\Command;

class LoadResources extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'finance:load-resources';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $client = ActiveCollabClient::make();
            $this->line('Начало импорта типов ресурсов');
            $this->line('Получение типов ресурсов');
            $types = $client->get('job-types')->getJson();
            if (isset($types['message'])) {
                $this->line($types['message']);
                return;
            }
            $this->withProgressBar($types, function ($type) {
                FinanceRes::makeFromCollab($type);
            });

            $this->newLine()->line('Импорт завершен');
        } catch (\Error|\Exception $e) {
            $this->error($e->getMessage());
            if (config('app.debug')) {
                $this->error($e->getTraceAsString());
            }
        }
    }
}
