<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Framework\ControllerConfig\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostController
{
    use RouterAwareAction;

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var Router
     */
    private $router;

    public function __construct(RendererInterface $renderer, PostRepository $postRepository, CategoryRepository $categoryRepository, Router $router)
    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        if ($id){
            return $this->show($request);
        }
        return $this->index($request);
    }

    /**
     * Acceuil des articles
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        $defaultParam = 1;
        $params = $request->getQueryParams();
        if (isset($params['p']) && is_numeric($params['p'])){
            $defaultParam = ($params['p']<=1) ? 1 : $params['p'];
        }
        $posts = $this->postRepository->findPaginatedPublic(12, $defaultParam);
        $categories = $this->categoryRepository->findAll();

        return $this->renderer->render('@blog/blog/index', compact('posts', 'categories'));
    }

    /**
     * detaille d'un article
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function show(ServerRequestInterface $request){
        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->findWithCategory($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/blog/show', [
            'post' => $post
        ]);
    }

}