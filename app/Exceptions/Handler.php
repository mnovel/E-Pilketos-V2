<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * Convert a validation exception into a JSON response.
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->errorResponse(
            $exception->errors(),
            'Validation failed',
            $exception->status
        );
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            // 🔹 Validation sudah ditangani di invalidJson()

            // 🔹 404
            if ($e instanceof NotFoundHttpException) {
                return $this->errorResponse(null, 'Not Found', 404);
            }

            // 🔹 401 Unauthorized
            if ($e instanceof UnauthorizedHttpException) {
                return $this->errorResponse(null, 'Unauthorized', 401);
            }

            // 🔹 403 Forbidden
            if ($e instanceof AccessDeniedHttpException) {
                return $this->errorResponse(null, 'Forbidden', 403);
            }

            // 🔹 Default error (500 atau lainnya)
            return $this->errorResponse(
                null,
                $e->getMessage(),
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500
            );
        }

        return parent::render($request, $e);
    }
}
