<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;

Route::get('/', [ItemController::class, 'index'])->name('home');

Route::post('/items/generate', [ItemController::class, 'generate'])->name('items.generate');
Route::delete('/items/clear', [ItemController::class, 'clear'])->name('items.clear');
Route::post('/items/set-sheet', [ItemController::class, 'setSheet'])->name('items.set-sheet');

Route::resource('items', ItemController::class);

Route::get('/fetch/{count?}', function ($count = null) {
    $kernel = Container::getInstance()->make(Kernel::class);
    
    $params = [];
    if ($count !== null && is_numeric($count)) {
        $params['--count'] = (int)$count;
    }
    
    $status = $kernel->call('sheets:get-comments', $params);
    
    return response($kernel->output())
           ->header('Content-Type', 'text/plain');
})->where('count', '\d+')->name('fetch');

Route::get('/sync', function ($count = null) {
    $kernel = Container::getInstance()->make(Kernel::class);
    
    $params = [];
    
    $status = $kernel->call('sheets:sync', $params);
    
    return redirect()->route('items.index')
            ->with('success', 'Экспорт выполнен');
})->name('sync');