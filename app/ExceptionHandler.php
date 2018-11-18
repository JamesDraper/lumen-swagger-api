<?php
declare(strict_types=1);

namespace App;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

use Laravel\Lumen\Exceptions\Handler;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Lib\JsonResponseBody;

use Exception;

/**
 * Exception handler.
 *
 * Returns an API error using the uniform JSON response body.
 */
class ExceptionHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class
    ];

    /**
     * @{inheritdoc}
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Exception  $e
     * @return string
     */
    public function render($request, Exception $e)
    {
        if (app()->isDev()) {
            $body = JsonResponseBody::createFailure([sprintf(
                "%s:\n %s",
                $e->getMessage(),
                $e->getTraceAsString()
            )])->toString();
        } else {
            $body = JsonResponseBody::createFailure([
                'An unexpected error has occurred, please try again shortly.'
            ])->toString();
        }

        return new Response($body, 500);
    }
}
