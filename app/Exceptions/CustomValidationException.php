<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends ResponseWithinDataException
{
    public function __construct(array $errors)
    {
        parent::__construct($errors, trans('system.bad_request'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
