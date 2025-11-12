<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\WarehouseProductController;


    Route::resource('manufacturings', ManufacturingController::class)->names('manufacturing');
    Route::resource('warehouse-products', WarehouseProductController::class)->names('manufacturing.warehouse-products');

