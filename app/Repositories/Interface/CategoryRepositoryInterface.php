<?php

namespace App\Repositories\Interface;

use App\Repositories\BaseRepositoryInterface;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function show($id);

    public function getCategoryById($id);

    public function create($data);

    public function getCategories();

    public function getCategoryFilters($data);

    public function deleteCategory($id);
}
