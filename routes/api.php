<?php

use App\Http\Controllers\API\AboutController;
use App\Http\Controllers\API\AdvertisingController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CheckOutController;
use App\Http\Controllers\API\ContactUsController;
use App\Http\Controllers\API\CustomerReviewController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\FeatureController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware'=>['check.lang']],function (){

    Route::group(['prefix'=>'v1/auth'],function (){
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('forget-Password', [AuthController::class, 'resetPassword']);
        Route::post('updateProfile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
        Route::get('me', [AuthController::class, 'singleUser'])->middleware('auth:api');
        // Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
        Route::post('myorders', [AuthController::class, 'myorder'])->middleware('auth:api');
        Route::post('myfavourite', [AuthController::class, 'myfavourite'])->middleware('auth:api');
        Route::post('password/send-otp', [AuthController::class, 'sendOtp'])->middleware('auth:api');
        Route::post('password/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('auth:api');
        Route::post('password/reset', [AuthController::class, 'resetPassword2'])->middleware('auth:api');
        Route::post('delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:api');
    });

    Route::group(['prefix'=>'v1/category'],function (){
         Route::get('single/{category_id}',[CategoryController::class,'singleCategory']);
            Route::get('/',[CategoryController::class,'allCategories']);
            Route::get('/pag',[CategoryController::class,'showAll']);

    });


    Route::group(['prefix'=>'v1/about'],function (){

        Route::get('/',[AboutController::class,'index']);

    });


    Route::group(['prefix'=>'v1/advertising'],function (){

        Route::get('/',[AdvertisingController::class,'showLand']);

    });


    Route::group(['prefix'=>'v1/feature'],function (){

        Route::get('/',[FeatureController::class,'index']);

    });

    Route::group(['prefix'=>'v1/review'],function (){
            Route::post('store/{product_id}', [ReviewController::class, 'store'])->middleware('auth:api');
            Route::get('show/{product_id}', [ReviewController::class, 'show']);
            Route::get('/', [ReviewController::class, 'showAll']);

    });

    Route::group(['prefix'=>'v1/customerReview'],function (){
            Route::post('store/', [CustomerReviewController::class, 'store'])->middleware('auth:api');
            Route::get('show/', [CustomerReviewController::class, 'show']);


    });

    Route::group(['prefix'=>'v1/product'],function (){
            Route::get('singleProduct/{product_id}',[ProductController::class,'singleProductWithRelated']);
            Route::get('/',[ProductController::class,'allProducts']);
            Route::get('/TopSeller',[ProductController::class,'TopSeller']);
            Route::get('/OffersProduct',[ProductController::class,'OffersProduct']);

    });

    Route::group(['prefix'=>'v1/cart','middleware'=>'auth:api'],function (){
            Route::post('addToCart/{product_id}', [CartController::class, 'addToCart']);

            Route::get('/', [CartController::class, 'viewCart']);

            Route::post('updateQuantity', [CartController::class, 'updateQuantity']);

            Route::get('removeFromCart/{id}', [CartController::class, 'removeFromCart']);

        });


        Route::group(['prefix'=>'v1/checkout','middleware'=>'auth:api'],function (){

            Route::post('/', [CheckOutController::class, 'checkout']);

        });

        Route::group(['prefix'=>'v1/contactus'],function (){

            Route::post('/', [ContactUsController::class, 'save']);

        });

    Route::group(['prefix'=>'v1/favourite','middleware'=>'auth:api'],function (){

            Route::post('/', [FavouriteController::class, 'toggleFavourite']);

        });


        Route::group(['prefix'=>'v1/setting'],function (){

            Route::get('/', [SettingController::class, 'index']);

        });


});

