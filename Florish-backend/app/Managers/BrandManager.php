<?php

namespace App\Managers;
use App\Models\Brand;

class BrandManager
{
    public function createBrand(array $brandData)
    {
        $brand = Brand::create($brandData);

        return $brand;
    }

    public function getBrandByIdWithRelations($id)
    {
        return Brand::with(['category'])->findOrFail($id);
    }

    public function getAllBrands($page = 1, $itemsPerPage = 10)
    {
        return Brand::with(['category'])->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getBrandById(string $id)
    {
        return Brand::find($id);
    }

    public function updateBrand(Brand $brand, array $data)
    {
        $brand->update($data);
    }

    public function deleteBrand(Brand $brand): void
    {
        $brand->delete();
    }
}