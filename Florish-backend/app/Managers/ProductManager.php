<?php

namespace App\Managers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductManager
{
    public function createProduct(array $productData)
    {
        $productData['stock_on_hand'] = 0;

        $productData['product_type_id'] = 1;

        $categoryId = $productData['category_id'];
        $productData['product_code'] = $this->generateProductCode($categoryId);
        return Product::create($productData);
    }

    public function getAllProducts($page, $itemsPerPage)
    {
        return Product::with(['productType', 'category.products', 'brand.products'])->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getTotalStockOnHand()
    {
        return Product::sum('stock_on_hand');
    }

    public function getProductByIdWithRelations($id)
    {
        return Product::with(['productType', 'category', 'brand'])
            ->findOrFail($id);
    }

    public function updateProduct(Product $product, array $data)
    {
        if ($data['barcode'] !== $product->barcode) {
            $validator = Validator::make($data, [
                'barcode' => 'required|unique:products',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Barcode is been used from other products.');
            }
        }

        $product->update($data);
        $product->save();
        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    public function generateProductCode($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $categoryCode = $category->category_code;

        $latestProduct = Product::where('category_id', $categoryId)->latest('id')->first();
        $newCodeNumber = ($latestProduct) ? intval(substr($latestProduct->product_code, -4)) + 1 : 1;
        $newProductCode = $categoryCode . '-' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

        return $newProductCode;
    }

    public function getCriticalStock($page)
    {
        return Product::with(['category', 'brand'])->where('stock_on_hand', '<=', DB::raw('reorder_level'))->paginate(2000, ['*'], 'page', $page);
    }

    public function getCriticalStockCount()
    {
        return Product::where('stock_on_hand', '<=', DB::raw('reorder_level'))->count();
    }
}
