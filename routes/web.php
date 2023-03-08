<?php

use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WidgetController::class, 'widgetCalculatorForm'])->name('home');
