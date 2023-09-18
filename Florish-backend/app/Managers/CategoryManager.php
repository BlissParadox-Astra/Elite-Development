<?php


namespace App\Managers;


use App\Models\Category;


class CategoryManager
{
    public function createCategory($categoryData)
    {
        $category = new Category();
        $category->category_name = $categoryData['category_name'];

        $categoryCode = strtoupper(substr($categoryData['category_name'], 0, 2));

        $uniqueCategoryCode = $this->generateUniqueCategoryCode($categoryCode);

        $category->category_code = $uniqueCategoryCode;

        $category->save();

        return $category;
    }


    private function generateUniqueCategoryCode($code, $attempt = 1)
    {
        $existingCategory = Category::where('category_code', $code)->first();

        if (!$existingCategory) {
            return $code;
        }

        $newCode = $code . $attempt;
        return $this->generateUniqueCategoryCode($newCode, $attempt + 1);
    }


    public function getAllCategories()
    {
        return Category::all();
    }


    public function getCategoryById(string $id)
    {
        return Category::find($id);
    }


    public function updateCategory(Category $category, array $data)
    {
        $category->update($data);

        $newCategoryCode = strtoupper(substr($category->category_name, 0, 2));

        $uniqueCategoryCode = $this->generateUniqueCategoryCode($newCategoryCode);

        $category->category_code = $uniqueCategoryCode;
        $category->save();

        return $category;
    }


    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
