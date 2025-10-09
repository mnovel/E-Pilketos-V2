<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            // ✅ Tangani ModelNotFoundException langsung
            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());
                return $this->errorResponse(null, "{$model} not found", 404);
            }

            // ✅ Tangani NotFoundHttpException yang membungkus ModelNotFoundException
            if ($e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException) {
                $model = class_basename($e->getPrevious()->getModel());
                return $this->errorResponse(null, "{$model} not found", 404);
            }

            // ✅ Tangani NotFoundHttpException murni (route tidak ditemukan)
            if ($e instanceof NotFoundHttpException) {
                $message = $e->getMessage();

                // Deteksi pesan model not found bawaan Laravel
                if (str_contains($message, 'No query results for model')) {
                    preg_match('/\[App\\\\Models\\\\(.*?)\]/', $message, $matches);
                    $model = $matches[1] ?? 'Resource';
                    return $this->errorResponse(null, "{$model} not found", 404);
                }

                // Kalau bukan model, berarti route
                return $this->errorResponse(null, 'Route not found', 404);
            }

            // ✅ Unauthorized
            if ($e instanceof UnauthorizedHttpException) {
                return $this->errorResponse(null, 'Unauthorized', 401);
            }

            // ✅ Forbidden
            if ($e instanceof AccessDeniedHttpException) {
                return $this->errorResponse(null, 'Forbidden', 403);
            }

            // ✅ Validasi
            if ($e instanceof ValidationException) {
                return $this->errorResponse(
                    $e->errors(),
                    'Validation failed',
                    $e->status
                );
            }

            // ✅ Default fallback
            return $this->errorResponse(
                null,
                $e->getMessage(),
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500
            );
        }

        return parent::render($request, $e);
    }
}
