<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Framework\ControllerConfig\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryShowController
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostRepository
     */
    private $postTable;

    /**
     * @var CategoryRepository
     */
    private $categoryTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostRepository $postTable, CategoryRepository $categoryTable) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    public function __invoke(Request $request)
    {
        $defaultParam=1;
        $params = $request->getQueryParams();
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        if (isset($params['p']) && is_numeric($params['p'])){
            $defaultParam = ($params['p']<=1) ? 1 : $params['p'];
        }
        $posts = $this->postTable->findPaginatedPublicForCategory(12, $defaultParam, $category->id);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;

        return $this->renderer->render('@blog/blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
