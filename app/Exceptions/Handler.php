<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        // Only handle Inertia requests (web routes)
        if (!$request->inertia()) {
            return $response;
        }

        $statusCode = $response->getStatusCode();

        // Handle specific HTTP errors with custom pages
        if ($e instanceof NotFoundHttpException || $statusCode === 404) {
            return Inertia::render('Errors/404')
                ->toResponse($request)
                ->setStatusCode(404);
        }

        if ($e instanceof AccessDeniedHttpException || $statusCode === 403) {
            return Inertia::render('Errors/403')
                ->toResponse($request)
                ->setStatusCode(403);
        }

        // Handle 500 errors and server exceptions
        if ($statusCode === 500 || $statusCode === 503) {
            return Inertia::render('Errors/500')
                ->toResponse($request)
                ->setStatusCode($statusCode);
        }

        return $response;
    }
}
