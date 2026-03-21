<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Favourite;
use Exception;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    use GeneralTrait;
    public function toggleFavourite(Request $request)
    {
         try
        {
             $user = auth()->user();

            $fav = Favourite::where('user_id',$user->id)
                    ->where('product_id',$request->product_id)
                    ->first();

            if($fav)
            {
                $fav->delete();

                return $this->ReturnSuccess(200,__('message.deleted'));

            }

            Favourite::create([
                'user_id'=>$user->id,
                'product_id'=>$request->product_id
            ]);

            return $this->ReturnSuccess(200,__('message.saved'));
        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function myFavourites()
    {
         try
        {
        $products = Favourite::with('product')
            ->where('user_id',auth()->id())
            ->get();

        return $this->ReturnData('favourites',$products,'My Favourite Products');
        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }
}
