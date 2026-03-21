<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Advertising;
use Illuminate\Http\Request;

class AdvertisingController extends Controller
{
    use GeneralTrait;
     public function showLand()
    {
        try
        {
            $advertise=Advertising::selection()
                ->where('status','1')
                ->latest()->get();
            return $this->ReturnData('advertise',$advertise,"");
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

}
