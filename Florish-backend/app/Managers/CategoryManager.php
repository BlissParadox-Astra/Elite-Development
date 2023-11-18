<?php


namespace App\Managers;


use App\Models\Category;
use Illuminate\Support\Facades\Validator;


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

    public function getPaginatedAllCategories($page, $itemsPerPage, $searchQuery = null)
    {
        $query = Category::query();

        if ($searchQuery) {
            $query->where('category_name', 'LIKE', '%'. $searchQuery . '%');
        }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getCategoryById(string $id)
    {
        return Category::find($id);
    }

    public function getCategoryCount()
    {
        return Category::count();
    }

    public function updateCategory(Category $category, array $data)
    {
        if ($data['category_name'] !== $category->category_name) {
            $validator = Validator::make($data, [
                'category_name' => 'required|unique:categories',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Category name already exists.');
            }
        }

        $newCategoryCode = strtoupper(substr($category->category_name, 0, 2));
        $category->update($data);
        $uniqueCategoryCode = $this->generateUniqueCategoryCode($newCategoryCode);
        $category->category_code = $uniqueCategoryCode;
        $category->save();

        return $category;
    }



    public function deleteCategory(Category $category)
    {
        try {
            $category->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1451) {
                throw new \Exception('Cannot delete category. In use by other records.');
            }
        }
    }
}
