<?php


namespace App\Controller\AdminController;


use Framework\Renderer\RendererInterface;

class AdminAcceuilController
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke()
    {
        return $this->renderer->render("@blog/admin/index");
    }
}