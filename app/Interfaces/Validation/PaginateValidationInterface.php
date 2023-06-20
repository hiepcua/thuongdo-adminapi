<?php


namespace App\Interfaces\Validation;


interface PaginateValidationInterface
{
    public function paginateMessage(): array;
    public function paginateRequest(): array;
}