<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;
use PDO;

class Repository
{

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Nom de la table en BDD
     * @var string
     */
    protected $table;

    /**
     * Entité à utiliser
     * @var string|null
     */
    protected $entity;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Pagine des éléments
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): PagerFanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        $fanta = new Pagerfanta($query);
        $fanta->setMaxPerPage($perPage);
        $fanta->setCurrentPage(($currentPage >= $fanta->getNbPages()) ? $fanta->getNbPages() : $currentPage);

        return $fanta;
    }

    /**
     * @return string
     */
    protected function paginationQuery():string
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Récupère une liste clef valeur de nos enregistrements
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Récupère tous les enregistrements
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    /**
     * Récupère une ligne par rapport à un champs
     *
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément à partir de son ID
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Met à jour un enregistrement au niveau de la base de données
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $query = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
    }

    /**
     * Crée un nouvel enregistrement
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = implode(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = implode(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $query->execute($params);
    }

    /**
     * Supprime un enregistrment
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $query->execute([$id]);
    }

    private function buildFieldQuery(array $params): string
    {
        return implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * @return mixed
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Vérifie qu'un enregistrement existe
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Permet d'éxécuter une requête et de récupérer le premier résultat
     *
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }
}
