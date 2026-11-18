<?php

namespace App\DataAccess\External;

use App\Domain\Interfaces\Infrastructure\IPdfGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

class DomPdfGenerator implements IPdfGenerator
{
    public function generate(string $view, array $data): string
    {
        return Pdf::loadView($view, $data)->output();
    }
}
