<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use App\Customs\Messages;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\ConnectionException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class Handler extends ExceptionHandler
{
    use Messages;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        if (env('ENABLE_BE_LOG_LIVE_ERRORS', false)) {
            if ($this->shouldReport($exception)) {
                Log::channel('be_errors_live')->emergency('```' . $exception->getMessage() . '```', [
                    'endpoint' => Request::fullUrl(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'payload' => Request::except($this->dontFlash),
                ]);
            }
        }

        if (env('ENABLE_BE_LOG_LOCAL_ERRORS', false)) {
            if ($this->shouldReport($exception)) {
                Log::channel('be_errors_local')->emergency('```' . $exception->getMessage() . '```', [
                    'endpoint' => Request::fullUrl(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'payload' => Request::except($this->dontFlash),
                ]);
            }
        }       

        parent::report($exception);        
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

		/**
		 * Use App\Traits\Messages@jsonErrorUnauthorizedAccess
		 * for AuthorizationException exception
		 */
		if ($exception instanceof AuthorizationException) {
			return $this->jsonErrorUnauthorizedAccess();
		}
		
		/**
		 * Use App\Traits\Messages@jsonErrorUnauthenticated
		 * for AuthenticationException exception
		 */
		if ($exception instanceof AuthenticationException) {
			return $this->jsonErrorUnauthenticated();
        }

		/**
		 * Use App\Traits\Messages@jsonErrorUnauthenticated
		 * for ConnectionException exception
		 */
		if ($exception instanceof ConnectionException) {
			return $this->jsonFailedResponse(null, 500, $exception->getMessage());
		}
	
        return parent::render($request, $exception);
    }    
}
