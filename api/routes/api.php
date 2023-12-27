<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController; 
use App\Http\Controllers\BookLoanController;
 
Route::post('/auth/login', [AuthController::class, 'login']); 
Route::middleware(['auth:sanctum', 'cors'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/user/change-password', [UsersController::class,'changePassword']); 
    Route::get('/books', [BooksController::class, 'index']);
    Route::get('/book/details/{id}', [BooksController::class, 'getBookDetails']);
    Route::post('/book_loans/create/{book_id}', [BookLoanController::class, 'store']); 
    Route::post('/book_loans/extend/{id}', [BookLoanController::class, 'extendDueDate']); 
    Route::get('/book_loans', [BookLoanController::class, 'index']);
    Route::middleware('admin')->group(function () {
       //users
    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/user/create', [UsersController::class, 'store']);
    Route::get('/user/{id}', [UsersController::class, 'getUser']);
    Route::put('/user/update/{id}', [UsersController::class, 'update']);
    Route::delete('/user/delete/{id}', [UsersController::class, 'destroy']);
    
    //books 
    Route::post('/book/create', [BooksController::class, 'store']);
    Route::post('/book/update/{id}', [BooksController::class, 'update']);
    Route::delete('/book/delete/{id}', [BooksController::class, 'destroy']);

    //books on loan  
     Route::post('/book_loans/return/{id}', [BookLoanController::class, 'returnBook']);
     Route::post('/book_loans/approve/{id}', [BookLoanController::class, 'approveBook']);
     Route::get('/book_loans/details/{id}', [BookLoanController::class, 'getBookDetails']);
     Route::get('/book_loans/user/{id}', [BookLoanController::class, 'getUserBooks']); 
     Route::post('/book_loans/penalty/{id}', [BookLoanController::class, 'Penalty']); 
     Route::post('/book_loans/pay_penalty/{id}', [BookLoanController::class, 'payPenalty']); 
     
     Route::post('/book_loans/update/{id}', [BookLoanController::class, 'update']);
     Route::delete('/book_loans/delete/{id}', [BookLoanController::class, 'destroy']);
    });
    
    
});