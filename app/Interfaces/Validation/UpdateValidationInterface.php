<?php


namespace App\Interfaces\Validation;


interface UpdateValidationInterface
{
    public function updateMessage(): array;

    public function updateRequest(string $id): array;
}