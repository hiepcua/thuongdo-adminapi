<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResponseException extends Exception
{
    private $_msg;
    private $_code;
    public function __construct($message = "", $code = Response::HTTP_INTERNAL_SERVER_ERROR, Throwable $previous = null)
    {
        $this->_msg = $message;
        $this->_code = $code;
        parent::__construct($message, $code, $previous);
    }

    public function render(): JsonResponse
    {
        return resError($this->_msg, $this->_code);
    }
}
