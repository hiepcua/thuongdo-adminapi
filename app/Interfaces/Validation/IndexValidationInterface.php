<?php


namespace App\Interfaces\Validation;


interface IndexValidationInterface
{
    public function indexMessage(): ?array;
    public function indexRequest(): array;
}