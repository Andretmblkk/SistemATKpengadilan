<?php
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin', function () {
    return redirect()->to('/admin/login');
})->name('admin');