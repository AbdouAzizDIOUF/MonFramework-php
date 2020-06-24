<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Série d'extensions concernant les textes
 *
 * @package Config\Twig
 */
class TextExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * Renvoie un extrait du contenu
     * @param string $content
     * @param int $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if ($content === null) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
