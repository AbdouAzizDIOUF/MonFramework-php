<?php
namespace App\Entity;

class Post
{

    public $id;

    public $name;

    public $slug;

    public $content;

    public $created_at;

    public $updated_at;

    public $category_name;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \DateTime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \DateTime($this->updated_at);
        }
    }
}
