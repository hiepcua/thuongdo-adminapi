<?php


namespace App\Interfaces;

interface ValidationCustomMessage
{
    public function indexMessage(): array;
    public function paginationMessage(): array;
    public function storeMessage(): array;
    public function updateMessage(): array;
    public function destroyMessage(): array;
}