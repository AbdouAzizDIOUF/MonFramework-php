<?php
namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i'): string
    {
        return '<span class="timeago" datetime="' . $date->format(\DateTime::ATOM) . '">' .
            $date->format($format) .
            '</span>';
    }
}
