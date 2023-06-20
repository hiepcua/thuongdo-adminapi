<?php


namespace App\Services\Carriers;

use App\Services\Service;

interface Carrier extends Service
{
    public const FAIL = -1;

    public function getPrice(array $array): float;

    public function getHeaders(): array;

    public function getParams(array $array): ?array;
}