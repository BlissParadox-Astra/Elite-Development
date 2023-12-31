<?php

namespace App\Managers;

use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandManager
{
    public function createBrand(array $brandData)
    {
        $brand = Brand::create($brandData);

        return $brand;
    }

    public function getBrandByIdWithRelations($id)
    {
        return Brand::with([
            'category'
        ])->findOrFail($id);
    }

    public function getAllBrands($page, $itemsPerPage, $searchQuery = null)
    {
        $query = Brand::with(['category'])
        ->orderBy('brands.created_at', 'desc');

        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                $query->where('brand_name', 'LIKE', '%'. $searchQuery . '%')
                ->orWhereHas('category', function ($query) use ($searchQuery) {
                    $query->where('category_name', 'LIKE', '%'. $searchQuery . '%');
                });
            });
        }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getBrandById(string $id)
    {
        return Brand::find($id);
    }

    public function updateBrand(Brand $brand, array $data)
    {
        if ($data['brand_name'] !== $brand->brand_name) {
            $validator = Validator::make($data, [
                'brand_name' => 'required|unique:brands',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Brand name already exists.');
            }
        }
        $brand->update($data);
        $brand->save();
        return $brand;
    }

    public function deleteBrand(Brand $brand)
    {
        try {
            $brand->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1451) {
                throw new \Exception('Cannot delete brand. In use by other records.');
            }
        }
    }
}
