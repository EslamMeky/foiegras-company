<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    use GeneralTrait;

     public function index()
    {
        try
        {
            $feature=Feature::selection()->latest()->get();
            return $this->ReturnData('feature',$feature,"get Feature Company");
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }
}
