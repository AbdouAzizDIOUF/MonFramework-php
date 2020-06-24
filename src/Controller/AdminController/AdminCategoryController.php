<?php
namespace App\Controller\AdminController;


use App\Repository\CategoryRepository;
use Framework\ControllerConfig\CrudController;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AdminCategoryController extends CrudController
{

    protected $viewPath = '@blog/admin/categories';

    protected $routePrefix = 'blog.category.admin';

    public function __construct(RendererInterface $renderer, Router $router, CategoryRepository $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }

    protected function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request):Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->slug('slug');
    }
}
