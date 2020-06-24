<?php
namespace Framework\Twig;

use Framework\Session\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash($type): ?string
    {
        return $this->flashService->get($type);
    }
}
