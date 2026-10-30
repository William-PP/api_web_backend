<?php

namespace App\Domain\Interfaces\Infrastructure;

interface IPdfGenerator
{
    public function generate(string $view, array $data): string;
}
