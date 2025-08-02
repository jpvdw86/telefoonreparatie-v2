<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigEnvExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnv', 'getenv'),
        ];
    }
}