<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(["prefix" => "/"], function(){
    Route::get('/', [IndexController::class, "index"]);
    Route::post('/registros', [IndexController::class, "getAllRegistros"])->name("registros.all");
    Route::post('/registros/add', [IndexController::class, "addRegistro"])->name("registros.add");
    Route::post('/registros/edit', [IndexController::class, "editRegistro"])->name("registros.edit");
    Route::post('/registros/delete', [IndexController::class, "deleteRegistro"])->name("registros.delete");
    Route::post('/registros/deletecompleted', [IndexController::class, "deleteCompleted"])->name("registros.deletecompleted");
    Route::post('/registros/count', [IndexController::class, "count"])->name("registros.count");
    Route::post('/registros/checkall', [IndexController::class, "checkAll"])->name("registros.checkall");
    Route::post('/registros/filter', [IndexController::class, "filterRegistro"])->name("registros.filter");
});
// Route::get('/', function () {
//     return view('index');
// });
