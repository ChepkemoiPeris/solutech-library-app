<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController; 
use App\Http\Controllers\BookLoanController;

//Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::post('/auth/logout', [AuthController::class, 'logout']);
//     Route::post('/books', [BookController::class, 'store']);
//     Route::put('/books/{id}', [BookController::class, 'update']);
//     Route::delete('/books/{id}', [BookController::class, 'destroy']);
//     Route::put('/books/borrow/{id}', [BookController::class, 'borrow']);
//     Route::put('/books/return/{id}', [BookController::class, 'return']);

//     Route::get('/books/getSession/getSession', [BookController::class, 'getSession']);
//     Route::get('/books/clearSession/clearSession', [BookController::class, 'clearSession']);

//     Route::get('/books', [BookController::class, 'index']);
//     Route::get('/books/{id}', [BookController::class, 'show']);
//     Route::get('/books/search/{property}/{value}', [BookController::class, 'search']);
// });

// // Routes that require authentication using Laravel Sanctum
Route::middleware(['auth:sanctum', 'cors'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/books', [BooksController::class, 'index']);
    Route::get('/book/details/{id}', [BooksController::class, 'getBookDetails']);
    Route::post('/book_loans/create/{book_id}', [BookLoanController::class, 'store']); 
    Route::middleware('admin')->group(function () {
       //users
    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/user/create', [UsersController::class, 'store']);
    Route::get('/user/{id}', [UsersController::class, 'getUser']);
    Route::post('/user/update/{id}', [UsersController::class, 'update']);
    Route::delete('/user/delete/{id}', [UsersController::class, 'destroy']); 
    //books 
    Route::post('/book/create', [BooksController::class, 'store']);
    Route::post('/book/update/{id}', [BooksController::class, 'update']);
    Route::delete('/book/delete/{id}', [BooksController::class, 'destroy']);

    //books on loan 
     Route::get('/book_loans', [BookLoanController::class, 'index']);
     Route::post('/book_loans/return/{id}', [BookLoanController::class, 'returnBook']);
     Route::post('/book_loans/approve/{id}', [BookLoanController::class, 'approveBook']);
     Route::get('/book_loans/details/{id}', [BookLoanController::class, 'getBookDetails']);
     Route::get('/book_loans/user/{id}', [BookLoanController::class, 'getUserBooks']); 
     
     Route::post('/book_loans/update/{id}', [BookLoanController::class, 'update']);
     Route::delete('/book_loans/delete/{id}', [BookLoanController::class, 'destroy']);
    });
    
    
});