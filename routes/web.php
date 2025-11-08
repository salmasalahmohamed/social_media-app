<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',[\App\Http\Controllers\HomeController::class,'index']
)->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/u/{user:username}',[\App\Http\Controllers\ProfileController::class,'index']
)->name('profile');
Route::get('/g/{group:slug}', [GroupController::class, 'profile'])
    ->name('group.profile');


Route::middleware('auth')->group(function () {
    Route::post('/profile/updateImages', [ProfileController::class, 'updateImage'])
        ->name('profile.updateImages');
    Route::post('/group/updateImages/{group}', [GroupController::class, 'updateImage'])
        ->name('group.updateImages');
    Route::post('create/group',[\App\Http\Controllers\GroupController::class,'create']);

    Route::post('/group/invite/{group:slug}', [GroupController::class, 'inviteUser'])
        ->name('group.inviteUser');

    Route::post('/group/join/{group:slug}', [GroupController::class, 'joinGroup'])
        ->name('group.joinGroup');


    Route::post('/accept/invitation/{user}', [GroupController::class, 'acceptInvitation'])
        ->name('group.acceptInvitation');

    Route::post('/reject/invitation/{user}', [GroupController::class, 'rejectInvitation'])
        ->name('group.rejectInvitation');
    Route::get('/group/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])
        ->name('group.approveInvitation');
    Route::post('/remove/user/{user}', [GroupController::class, 'removeUser'])
        ->name('group.removeUser');
    Route::post('post/create',[\App\Http\Controllers\PostController::class,'create'])->name('post.store');
    Route::post('post/update/{post}',[\App\Http\Controllers\PostController::class,'update'])->name('post.update');
    Route::post('post/delete/{post}',[\App\Http\Controllers\PostController::class,'delete'])->name('post.delete');
    Route::delete('photo/delete/{photo}',[\App\Http\Controllers\PostController::class,'deletephoto'])->name('photo.delete');
    Route::get('post/download/{attachment}',[\App\Http\Controllers\PostController::class,'downloadAttachment'])->name('post.download');
    Route::post('post/{post}/reaction',[\App\Http\Controllers\PostController::class,'postReaction'])->name('post.reaction');
    Route::post('/comment/create/{post}',[\App\Http\Controllers\PostController::class,'commentCreate'])->name('post.comment');
    Route::delete('comment/delete/{comment}',[\App\Http\Controllers\PostController::class,'deleteComment'])->name('comment.delete');
    Route::post('comment/update/{comment}',[\App\Http\Controllers\PostController::class,'updateComment'])->name('comment.update');
    Route::post('comment/reaction/{comment}',[\App\Http\Controllers\PostController::class,'commentReaction'])->name('comment.reaction');
    Route::get('posts',[\App\Http\Controllers\HomeController::class,'posts']);
    Route::post('user/follow/{user}',[\App\Http\Controllers\UserController::class,'follow'])->name('user.follow');


//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
