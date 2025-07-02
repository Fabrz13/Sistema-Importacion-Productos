<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Soap\ImportacionSoapController;

Route::any('/soap/importacion', [ImportacionSoapController::class, 'handle']);
Route::get('/soap/importacion?wsdl', [ImportacionSoapController::class, 'wsdl']);