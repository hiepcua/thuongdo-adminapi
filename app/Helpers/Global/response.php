<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

if (!function_exists('writeLog')) {
    function writeLog(?string $msg, ?int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['code' => $code, 'message' => $msg ?? __('system.successfully')], $code);
    }
}

if (!function_exists('writeLogWithData')) {
    function writeLogWithData($data, ?string $msg = null, ?int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(
            ['code' => $code, 'message' => $msg ?? __('system.successfully'), 'data' => $data],
            $code
        );
    }
}

if (!function_exists('responseSuccess')) {
    function resSuccess(?string $msg = null): JsonResponse
    {
        return writeLog($msg ?? __('system.successfully'));
    }
}

if (!function_exists('resSuccessWithinData')) {
    function resSuccessWithinData($data, ?string $msg = null): JsonResponse
    {
        return writeLogWithData($data, $msg);
    }
}

if (!function_exists('resError')) {
    function resError(?string $msg = null, ?int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return writeLog($msg ?? __('system.failed'), $code);
    }
}

if (!function_exists('resErrorWithinData')) {
    function resErrorWithinData($data, ?string $msg = 'Fail', ?int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return writeLogWithData($data, $msg ?? __('system.failed'), $code);
    }
}

if (!function_exists('resValidation')) {
    function resValidation($data): JsonResponse
    {
        return writeLogWithData($data, __('system.bad_request'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}