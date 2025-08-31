<?php

namespace App\Exceptions;

use Http;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use \App\Traits\ApiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof HttpException){
            $code = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'Not Found';
            return $this->errorResponse($message, $code);

        }
        //modelNotFoundException for findOrFail
        if($exception instanceof ModelNotFoundException){
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exist any {$modelName} with the specified identificator", Response::HTTP_NOT_FOUND);   

        }
        //authorizationException
        if($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(), Response::HTTP_FORBIDDEN);   

        }
        //AuthenticationException
        if($exception instanceof \Illuminate\Auth\AuthenticationException){
            return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);   

        }
        //validationException
        if($exception instanceof ValidationException){
            $errors = $exception->validator->errors()->getMessages();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);   

        }
        if(env('APP_DEBUG', false)){
            return parent::render($request, $exception);
        }
        //unexpectedException
        return $this->errorResponse('Unexpected Exception. Try later', Response::HTTP_INTERNAL_SERVER_ERROR);

        
    }
}
