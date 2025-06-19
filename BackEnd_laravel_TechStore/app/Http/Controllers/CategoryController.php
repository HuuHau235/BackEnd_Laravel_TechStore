<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getCategoriesByID()
    {
        $categories = $this->categoryService->getCategoriesByID();
        return response()->json($categories);
    }
}
