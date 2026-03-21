<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Feature;
use App\Models\PopularProduct;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use GeneralTrait;

    public function singleCategory($category_id)
    {
        try
        {
            $locale = app()->getLocale();
           $category =Category::select([
                'id',
                'title_'.$locale.' as title' ,
                'desc_'.$locale.' as desc',
                'image',
                'features_ids',
                 'popular_products_ids',
                'created_at',
                'updated_at'])
           ->where('id',$category_id)->first();


            // Features
            $category->features = Feature::whereIn('id', $category->features_ids ?? [])
                ->get(['id', 'title_'.$locale.' as title']);

            // Popular Products
            $category->popular_products = PopularProduct::whereIn('id', $category->popular_products_ids ?? [])
                ->get(['id', 'title_'.$locale.' as title']);
           if (!$category)
           {
               return $this->ReturnError(404,__('message.notFoundCategory'));
           }
           return $this->ReturnData('category',$category,'get this Category');

        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function allCategories()
    {
        try
        {
            $locale = app()->getLocale();
            $categories=Category::select([
                'id',
                 'title_'.$locale.' as title' ,
                'desc_'.$locale.' as desc',
                'image',
                'features_ids',
                'popular_products_ids',
                'created_at',
                'updated_at'
            ])->latest()->get();

            foreach ($categories as $category) {
                $category->features = Feature::whereIn('id', $category->features_ids ?? [])
                    ->get(['id', 'title_'.$locale.' as title']);

                $category->popular_products = PopularProduct::whereIn('id', $category->popular_products_ids ?? [])
                    ->get(['id', 'title_'.$locale.' as title']);
            }

            return $this->ReturnData('categories',$categories,'get all categories');

        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }
    public function showAll()
    {
        try
        {

            $locale = app()->getLocale();
            $categories=Category::select([
                'id',
                 'title_'.$locale.' as title' ,
                'desc_'.$locale.' as desc',
                'image',
                'features_ids',
                'popular_products_ids',
                'created_at',
                'updated_at'
            ])
            ->latest()->paginate(10);
           foreach ($categories as $category) {
            $category->features = Feature::whereIn('id', $category->features_ids ?? [])
                ->get(['id', 'title_'.$locale.' as title']);

            $category->popular_products = PopularProduct::whereIn('id', $category->popular_products_ids ?? [])
                ->get(['id', 'title_'.$locale.' as title']);
        }
            return $this->ReturnData('categories',$categories,'get pagination 10 categories');

        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }
}
