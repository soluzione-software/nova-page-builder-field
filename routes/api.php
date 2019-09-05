<?php

use DigitalCloud\PageBuilderField\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', Controller::class . '@index');
Route::post('/', Controller::class . '@store');
