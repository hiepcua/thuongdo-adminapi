<?php


namespace App\Interfaces\Validation;


interface DestroyValidationInterface
{
    public function destroyMessage(): array;
    public function destroyRequest(): array;
}