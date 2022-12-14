<?php


Route::get('/products', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show']);


Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('products', App\Http\Controllers\ProductController::class)->except(['index', 'show']);

    Route::get('products/{product}/prices', [App\Http\Controllers\ProductPriceController::class, 'index']);
    Route::name('prices.')->group(function() {
        Route::post('prices', [App\Http\Controllers\PriceController::class, 'store'])->name('store');
        Route::put('prices/{price}', [App\Http\Controllers\PriceController::class, 'update'])->name('update');
        Route::delete('prices/{price}', [App\Http\Controllers\PriceController::class, 'destroy'])->name('destroy');
    });
});

