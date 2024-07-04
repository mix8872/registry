<?php

namespace App\Http\Controllers\Api;

use App\Classes\ActiveCollabHooks;
use Illuminate\Routing\Controller;

class HooksController extends Controller
{
    public function collabHook()
    {
        $data = request()->post();
        return match (true) {
            empty($data['type']),
            empty($data['payload']),
            !isset(ActiveCollabHooks::$events[$data['type']]),
            !method_exists(ActiveCollabHooks::class, lcfirst($data['type'])) => response()->json(['success' => false], 400),
            default => (new ActiveCollabHooks($data))->{lcfirst($data['type'])}()
        };
    }
}
