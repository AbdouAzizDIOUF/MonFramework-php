<?php


namespace App\Routes;

use App\Controller\CategoryShowController;
use App\Controller\PostController;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', dirname(__DIR__, 2) .'/views');
        //var_dump(dirname(__DIR__, 2) .'/views/blog');
        $blogPrefix = $container->get('blog.prefix');
        $container->get(Router::class)->addRoute($blogPrefix, PostController::class, 'blog.index');
        $container->get(Router::class)->addRoute("$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}", PostController::class, 'blog.show');
        $container->get(Router::class)->addRoute("$blogPrefix/category/{slug:[a-z\-0-9]+}", CategoryShowController::class, 'blog.category');

        /* if ($container->has('admin.prefix')) {
             $prefix = $container->get('admin.prefix');
             $container->get(Router::class)->crud("$prefix/posts", AdminPostController::class, 'blog.admin');
             $container->get(Router::class)->crud("$prefix/categories", AdminCategoryController::class, 'blog.category.admin');
         }*/
    }
}
