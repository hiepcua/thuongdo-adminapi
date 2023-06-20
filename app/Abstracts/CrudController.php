<?php


namespace App\Abstracts;


use App\Http\Controllers\Controller;
use App\Interfaces\ValidationCustomMessage;
use App\Interfaces\ValidationRequest;

abstract class CrudController extends Controller implements ValidationRequest, ValidationCustomMessage
{
    public function indexRequest(): array
    {
        return [];
    }

    public function paginationRequest(): array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [];
    }

    public function updateRequest(): array
    {
        return [];
    }

    public function destroyRequest(): array
    {
        return [];
    }

    public function indexMessage(): array
    {
        return [];
    }

    public function paginationMessage(): array
    {
        return [];
    }

    public function storeMessage(): array
    {
        return [];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function destroyMessage(): array
    {
        return [];
    }
}