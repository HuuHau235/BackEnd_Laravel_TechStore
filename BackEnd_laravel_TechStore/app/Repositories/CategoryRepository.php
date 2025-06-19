<?php
namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getCategoriesByID()
    {
        return Category::whereHas('blogs')->get();
    }
}
