<?php

namespace App\Repository;

use App\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Repository;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository
{

    protected $entity = Post::class;

    protected $table = 'posts';

    public function findPaginatedPublic(int $perPage, int $currentPage): PagerFanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            'SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM posts as p 
                LEFT JOIN categories as c ON c.id = p.category_id 
                ORDER BY p.created_at DESC',
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        $fanta = new Pagerfanta($query);
        $fanta->setMaxPerPage($perPage);
        $fanta->setCurrentPage(($currentPage >= $fanta->getNbPages()) ? $fanta->getNbPages() : $currentPage);

        return $fanta;
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            'SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM posts as p 
                LEFT JOIN categories as c ON c.id = p.category_id 
                WHERE p.category_id = :category
                ORDER BY p.created_at DESC',
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );
        $fanta = new Pagerfanta($query);
        $fanta->setMaxPerPage($perPage);
        $fanta->setCurrentPage(($currentPage >= $fanta->getNbPages()) ? $fanta->getNbPages() : $currentPage);

        return $fanta;
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail('
            SELECT p.*, c.name category_name, c.slug category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            WHERE p.id = ?', [$id]);
    }

    /**
     * @return string
     */
    protected function paginationQuery():string
    {
        return "SELECT p.id, p.name, c.name category_name
        FROM {$this->table} as p
        LEFT JOIN categories as c ON p.category_id = c.id
        ORDER BY created_at DESC";
    }
}
