<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        try
        {
            $about=About::selection()->latest()->get();
            return $this->ReturnData('about',$about,"get about Company");
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

}
