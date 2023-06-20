<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
//        OAuthServerException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(
            function (NotFoundHttpException $e, $request) {
                return resError(__('system.page_not_found'), Response::HTTP_NOT_FOUND);
            }
        );
    }


    /**
     * @param  Throwable  $e
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        if ($e instanceof OAuthServerException && $e->getCode() == 9) {
            return;
        }
        parent::report($e);
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        $errors = [];
        foreach ($exception->errors() as $key => $value) {
            $errors[$key] = array_shift($value);
        }
        return resValidation($errors);
    }

    /**
     * @param $request
     * @param  Throwable  $e
     * @return JsonResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $model = explode('\\', $e->getModel());
            $modelPhrase = ucwords(implode(' ', preg_split('/(?=[A-Z])/', end($model))));
            $class = 'App\Models\\'.str_replace(" ", "", App::make($e->getModel())->modelNotFoundMessage ?? $modelPhrase);
            return resError(
                trans(
                    'system.no_content',
                    [
                        'attribute' => ((new $class)->getTableFriendly())
                    ]
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        if ($e instanceof AuthenticationException) {
            return resError(__('auth.must_logged'), Response::HTTP_UNAUTHORIZED);
        }
        return parent::render($request, $e);
    }
}
