<?php

use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('todo')->group(function () {
    Route::post('/', [TodoController::class, 'store'])->name('todo.store');
    Route::get('/export-excell', [TodoController::class, 'exportExcell'])->name('todo.export-excell');
});
