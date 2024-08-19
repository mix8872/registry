<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Log;

class ExceptionMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);
        if ($e = $response->exception) {
            return $this->registerExceptionHandler($e);
        }
        return $response;
    }

    protected function registerExceptionHandler(\Exception|\Error $e)
    {
        $error = [
            'success' => false,
            'message' => $e->getMessage()
        ];

        $errCode = $e->getCode();
        $errCode = $errCode < 600 && $errCode >= 200 ? $errCode : 500;

        if ($errCode === 500 && config('app.debug')) {
            $code = $e->getCode();
            $message = $e->getMessage();

            $error['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ];

            Log::error("request error: $code - $message \n" . request()->getContent() . "\ntrace:\n" . $e->getTraceAsString());
        }
        return response()->json($error, $errCode ?: 400);
    }
}
