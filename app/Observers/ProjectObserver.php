<?php

namespace App\Observers;

use App\Classes\ActiveCollabClient;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    public const NOTIFICATION_DURATION = 30;

    public function saved(Model $r)
    {
        if (config('services.collab.pass') && isset($r->getChanges()['status'])) {
            try {
                $client = ActiveCollabClient::make();
                switch ($r->getChanges()['status']) {
                    case Project::STATUS_ARCHIVED:
                        $client->put("complete/project/{$r->crm_id}");
                        Notification::make()->title('Проект был также архивирован в ActiveCollab')
                            ->seconds(self::NOTIFICATION_DURATION)
                            ->warning()
                            ->color('warning')
                            ->send();
                        break;
                    case Project::STATUS_ACTIVE:
                        $client->put("open/project/{$r->crm_id}");
                        Notification::make()->title('Проект был также разархивирован в ActiveCollab')
                            ->seconds(self::NOTIFICATION_DURATION)
                            ->warning()
                            ->color('success')
                            ->send();
                        break;
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Notification::make()->title($e->getMessage)->danger()->send();
            }
        }
    }
}
