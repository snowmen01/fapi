<?php

namespace App\Repositories\Repository;

use App\Models\Category;
use App\Repositories\BaseRepository;
use App\Repositories\Interface\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected $category;

    public function __construct(
        Category $category
    ) {
        $this->category = $category;
    }

    public function show($id)
    {
        $category = $this->category->find();

        return $category;
    }

    public function getCategoryById($id)
    {
        $category = $this->category->find($id);

        return $category;
    }

    public function create($data)
    {
        $category = $this->category->create($data);

        return $category;
    }

    public function getCategories()
    {
        $categories = $this->category->all();

        return $categories;
    }

    public function deleteCategory($id)
    {
        $category = $this->getCategoryById($id);
        $category->delete();

        return $category;
    }

    public function getCategoryFilters($data)
    {
        $categories = $this->category
            ->when(isset($data['name']), function ($query) use ($data) {
                return $query->where('name', 'LIKE', '%' . $data['name'] . '%');
            })
            ->when(isset($data['description']), function ($query) use ($data) {
                return $query->where('description', 'LIKE', '%' . $data['description'] . '%');
            })
            ->when(isset($data['slug']), function ($query) use ($data) {
                return $query->where('slug', 'LIKE', '%' . $data['slug'] . '%');
            })
            ->when(isset($data['active']), function ($query) use ($data) {
                return $query->where('active', $data['active']);
            })
            ->orderBy('id', 'desc')
            ->filter($data);

        return $categories->get();
    }
}
