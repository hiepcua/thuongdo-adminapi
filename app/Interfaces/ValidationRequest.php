<?php


namespace App\Interfaces;



interface ValidationRequest
{
    public function indexRequest(): array;
    public function paginationRequest(): array;
    public function storeRequest(): array;
    public function updateRequest(): array;
    public function destroyRequest(): array;
}