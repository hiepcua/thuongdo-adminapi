<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseWithinDataException extends Exception
{
    protected $_errors;
    protected $_msg;
    protected $_code;

    public function __construct(array $errors, ?string $msg = '', int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $this->_errors = $errors;
        $this->_msg = $msg;
        $this->_code = $code;
        parent::__construct($msg, $code);
    }

    public function render(): JsonResponse
    {
        return resErrorWithinData($this->_errors, $this->_msg, $this->_code);
    }
}
