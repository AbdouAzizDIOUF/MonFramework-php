<?php

namespace App\Routes;


use App\Controller\AdminController\AdminAcceuilController;
use App\Controller\AdminController\AdminCategoryController;
use App\Controller\AdminController\AdminPostController;
use Framework\Module;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AdminModule extends Module
{

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $prefix = $container->get('admin.prefix');

        //$container->get(RendererInterface::class)->addPath('admin', dirname(__DIR__, 2) . '/views/admin');

        $container->get(Router::class)->crud("$prefix", AdminAcceuilController::class, 'blog.admin');
        $container->get(Router::class)->crud("$prefix/posts", AdminPostController::class, 'blog.post.admin');
        $container->get(Router::class)->crud("$prefix/categories", AdminCategoryController::class, 'blog.category.admin');
    }
}
