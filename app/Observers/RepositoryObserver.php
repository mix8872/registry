<?php

namespace App\Observers;

use Gitlab\Client;
use Illuminate\Database\Eloquent\Model;

class RepositoryObserver
{
    /**
     * Handle the Model "retrieved" event.
     *
     * @param Model $r
     * @return void
     */
    public function retrieved(Model $r)
    {
//        (new Client())->jobs()->all();

        /*$path = ltrim($r->url, env('GITLAB_URL'));
        $projects = \GrahamCampbell\GitLab\Facades\GitLab::projects()->all(['search_namespaces' => true, 'search' => $path]);
        if (!$projects) {
            return;
        }
        $id = $projects[0]['id'];
        $jobs = \GrahamCampbell\GitLab\Facades\GitLab::jobs()->all($path, ['scope' => 'manual']);

        $a = 1;*/
    }
}
