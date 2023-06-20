<?php


namespace App\Interfaces\Validation;


interface StoreValidationInterface
{
    public function storeMessage(): ?array;

    public function storeRequest(): array;
}