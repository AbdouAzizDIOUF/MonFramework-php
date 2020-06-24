<?php
namespace Framework\ControllerConfig;

use Framework\Database\NoRecordException;
use Framework\Database\Repository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudController
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Repository
     */
    protected $table;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié"
    ];

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, Repository $table, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash;
    }


    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * Affiche la liste des éléments
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $val_param = 1;
        $params = $request->getQueryParams();
        if (isset($params['p']) && is_numeric($params['p'])){
            $val_param = ($params['p']<=1) ? 1 : $params['p'];
        }

        $items = $this->table->findPaginated(12, $val_param);

        return $this->renderer->render($this->viewPath.'/index', compact('items'));
    }

    /**
     * Edite un élément
     * @param Request $request
     * @return ResponseInterface|string
     * @throws NoRecordException
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;

            return $this->renderer->render(
                $this->viewPath.'/edit',
                $this->formParams(compact('item', 'errors'))
            );
        }

        return $this->renderer->render(
            $this->viewPath.'/edit',
            $this->formParams(compact('item'))
        );
    }

    /**
     * Crée un nouvel élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $item = $params;
            $errors = $validator->getErrors();

            return $this->renderer->render(
                $this->viewPath.'/create',
                $this->formParams(compact('item', 'errors'))
            );

        }

        return $this->renderer->render($this->viewPath.'/create',
            $this->formParams(compact('item'))
        );
    }

    /**
     * Action de suppression
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request): ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Filtre les paramètres reçu par la requête
     *
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, [], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Génère le validateur pour valider les données
     *
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request):Validator
    {
        return new Validator($request->getParsedBody());
    }

    /**
     * Génère une nouvelle entité pour l'action de création
     *
     * @return mixed
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     *
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
