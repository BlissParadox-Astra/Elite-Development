<?php


namespace App\Managers;


use App\Models\Category;


class CategoryManager
{
    public function createCategory($categoryData)
    {
        $category = new Category();
        $category->category_name = $categoryData['category_name'];


        // Generate a category code based on the first two characters of the category name (uppercase)
        $categoryCode = strtoupper(substr($categoryData['category_name'], 0, 2));


        // Check if the generated code is unique; if not, append a number to make it unique
        $uniqueCategoryCode = $this->generateUniqueCategoryCode($categoryCode);


        $category->category_code = $uniqueCategoryCode;


        $category->save();


        return $category;
    }


    private function generateUniqueCategoryCode($code, $attempt = 1)
    {
        // Check if a category with the generated code already exists
        $existingCategory = Category::where('category_code', $code)->first();


        if (!$existingCategory) {
            return $code; // Code is unique; return it
        }


        // If a category with the code exists, append a number and try again
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
    }


    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
