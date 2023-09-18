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

    public function getAllBrands()
    {
        return Brand::with(['category'])->get();
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