<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ForgotPasswordController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('signup',[UserController::class, 'signUp']);
Route::post('login', [UserController::class,'login']);
Route::post('viewCategories',[UserController::class,'viewCategory']);
Route::post('viewSubCategory',[SubcategoryController::class,'index']);
Route::post('addItem',[ItemsController::class,'store']);
Route::post('viewItems',[ItemsController::class,'index']);
Route::get('items',[ItemsController::class,'show']);
Route::Post('addShop',[ShopController::class,'store']);
Route::post('viewShop',[ShopController::class,'index']);
Route::post('joinShop',[ShopController::class,'joinShopItems']);
Route::post('viewShopItem',[ShopController::class,'singleShop']);
Route::post('generalOrder',[UserController::class,'allOrderInGeneral']);
Route::post('forget',[ForgotPasswordController::class,'forgot']);
Route::post('password/reset',[ForgotPasswordController::class,'reset']);
Route::post('changeItem',[ItemsController::class,'makeitem']);
Route::post('myProfile2',[StaffController::class, 'index']);
Route::post('pending',[OrdersController::class,'pending']);
Route::post('makeOrderChanges2',[OrdersController::class,'approve']);
Route::post('paidYear33',[OrdersController::class,'paidYear']);

Route::group(['middleware' => ['jwt.Client']], function () {
Route::post('profile',[ClientsController::class,'index']);
Route::post('makeOrder',[OrdersController::class,'store']);
Route::post('myOrder',[OrdersController::class,'index']);
Route::post('cancelMyOrder',[OrdersController::class,'approve']);
Route::post('logout', [UserController::class,'logout']);
Route::post('changePassword',[StaffController::class,'changePassword']);

 });
 Route::group(['middleware' => ['jwt.Staff']], function () {
 Route::post('myProfile',[StaffController::class, 'index']);
 Route::post('makeOrderChanges',[OrdersController::class,'approve']);
 Route::post('allDailyOrder',[OrdersController::class,'show']);
 Route::post('myWork',[OrdersController::class,'myWorker']);
 Route::post('weeklyOrder',[OrdersController::class,'myWorkerWeekly']);
 Route::post('logout2', [UserController::class,'logout']);
 Route::post('changePassword2',[StaffController::class,'changePassword']);
 });
Route::group(['middleware' => ['jwt.Super']], function () {
Route::post('addStaff',[StaffController::class, 'store']);
Route::post('addCategory',[CategoryController::class,'store']);
Route::post('addSubCategory',[SubcategoryController::class,'store']);
Route::post('allDailyOrder2',[OrdersController::class,'show']);
Route::post('myWork2',[OrdersController::class,'myWorker']);
Route::post('allOrderPaid',[OrdersController::class,'allActivity']);
Route::post('allStaff',[StaffController::class,'allstaff']);
Route::post('weeklyOrder2',[OrdersController::class,'myWorkerWeekly']);
Route::post('weekly',[OrdersController::class,'adminWeekly']);
Route::post('month',[OrdersController::class,'adminMonthly']);
Route::post('year',[OrdersController::class,'adminYear']);
Route::post('cancelled',[OrdersController::class,'cancelled']);
Route::post('logout3', [UserController::class,'logout']);
Route::post('allOrder',[OrdersController::class,'allOrderPerYear']);
Route::post('allOrdercancelled',[OrdersController::class,'allOrderPerYearCancelled']);
Route::post('changePassword3',[StaffController::class,'changePassword']);
Route::post('weekCancelled',[OrdersController::class,'allOrderPerWeekCancelled']);
Route::post('weekOrdered',[OrdersController::class,'allOrderPerWeek']);
Route::post('paidToday',[OrdersController::class,'paidToday']);
Route::post('paidWeekly',[OrdersController::class,'paidWeekly']);
Route::post('paidMonthly',[OrdersController::class,'paidMonthly']);
Route::post('paidYear',[OrdersController::class,'paidYear']);
Route::post('cancelledToday',[OrdersController::class,'cancelledToday']);
 });