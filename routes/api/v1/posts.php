<?php

use App\Http\Controllers\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::bind('postResource', function ($value) {
    return App\Models\Post::withTrashed()
        ->where('id', $value)
        ->firstOrFail();
});

Route::bind('post', function ($value) {
    return App\Models\Post::withTrashed()
        ->where('slug', $value)
        ->first()
        ?? App\Models\Post::withTrashed()
            ->whereKey($value)
            ->firstOrFail();
});

Route::middleware('auth:sanctum')->prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index')->withoutMiddleware('auth:sanctum');
    Route::post('/', [PostController::class, 'store'])->name('posts.store');
    Route::get('/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->withoutMiddleware('auth:sanctum')
        ->middleware('web');
    Route::put('/{postResource}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/{postResource}', [PostController::class, 'delete'])->name('posts.delete');
    Route::put('/{postResource}/restore', [PostController::class, 'restore'])->name('posts.restore');
});
