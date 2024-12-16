<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::name('api.')->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/v1/*' // Disable csrf verification for api route - (Only for test case)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e, Request $request) {
            if (!$request->is('api/*')) {
                return null; // API dışı istekler için default render içeriğini kullan
            }

            $status = match (true) {
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => 404,
                $e instanceof QueryException => 500,
                default => 500,
            };

            $message = match ($status) {
                404 => $e->getMessage() ?? 'Record not found.',
                500 => $e instanceof QueryException ? 'Database query error.' : 'An error has occurred.',
            };

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        });
    })->create();
