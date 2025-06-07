<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            // Solo respuestas JSON para APIs
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }

            // Para otros casos, usar el render por defecto
            return null;
        });
    }

    protected function handleApiException(Throwable $e): JsonResponse
    {
        // Error de validación
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => __('validation.failed'),
                'errors' => $e->errors(),
            ], 422);
        }

        // Error de autenticación
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        // Errores HTTP estándar (como 403, 404, etc.)
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $e->getMessage() ?: __('http.' . $e->getStatusCode()),
            ], $e->getStatusCode());
        }

        // Otros errores no controlados (500)
        return response()->json([
            'message' => __('errors.internal_server_error'),
        ], 500);
    }
}
