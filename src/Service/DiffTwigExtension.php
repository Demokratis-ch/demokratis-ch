<?php

declare(strict_types=1);

namespace App\Service;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DiffTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('wordDiff', function (string $old, string $new) {
                $diff = (new WordDiff())->diff($old, $new);

                return array_reduce($diff, function (?string $out, array $span) {
                    [$type, $text] = $span;
                    $classes = match ($type) {
                        -1 => 'bg-red-100 line-through text-red-800',
                        1 => 'bg-green-100 text-green-800',
                        default => '',
                    };

                    return $out.'<span class="'.$classes.'">'.nl2br($text).'</span>';
                });
            }, ['is_safe' => ['html']]),
        ];
    }
}
