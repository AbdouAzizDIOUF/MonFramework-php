<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    /**
     * Container
     * @var ContainerInterface
     */
    private $container;

    /**
     * App constructor.
     * @param ContainerInterface $container
     * @param string[] $modules Liste des modules à charger
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) && in_array($parsedBody['_method'], ['DELETE', 'PUT'])) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        if ($uri==='/'){
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', '/blog');
        }
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if ($route === null) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = $callback($request);
        if (is_string($response)) {
            return new Response(200, [], $response);
        }

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        throw new \RuntimeException('The response is not a string or an instance of ResponseInterface');
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
