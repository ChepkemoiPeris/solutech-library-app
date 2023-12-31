<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookLoan;
use App\Models\Books;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class BookLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query();

        $page = $query['page'] ?? 1;
        $limit = $query['limit'] ?? 10;

        $searchTerm = $query['email'] ?? null;  

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $queryBuilder = BookLoan::with('book', 'user');

        if ($searchTerm) {
            $queryBuilder->whereHas('user', function ($query) use ($searchTerm) {
                $query->where('email', 'like', "%{$searchTerm}%"); 
            });
        }

        $bookLoans = $queryBuilder->paginate($limit);

        return response()->json($bookLoans);
    }


    public function store(Request $request, $book_id)
    {
        $user_id = $request->input('user_id');
    
        if (!$user_id || !$book_id) {
            return response()->json(['error' => 'Supply required inputs'], Response::HTTP_CONFLICT);
        }
    
        $bookLoan = BookLoan::where('book_id', $book_id)
            ->whereIn('status', ['borrowed', 'pending'])
            ->first();
    
        if ($bookLoan) {
            return response()->json(['error' => 'Book not available'], Response::HTTP_CONFLICT);
        }
    
        $exists = Books::where('id', $request->book_id)->first();
        if (!$exists) {
            return response(['message' => 'Book does not exist'], Response::HTTP_CONFLICT);
        }
     
        $due_date = Carbon::now()->addWeek(); 
        $status = $request->input('status', 'pending');
    
        $validatedData = [
            'book_id' => $book_id,
            'user_id' => $user_id,
            'added_by' => Auth::id(),
            'status' => $status,
            'due_date' => $due_date,
        ];
    
        $book_loan = BookLoan::create($validatedData);
    
        return response(['message' => 'Book pending approval'], Response::HTTP_OK);
    }
    

    public function getBookDetails(string $id)
    {
        $book = BookLoan::findOrFail($id);
        $book->image_url = asset('storage/' . $book->image);

        return response()->json($book);
    }
    public function extendDueDate(string $id)
    {
        
        $bookLoan = BookLoan::findOrFail($id); 
        if (!$bookLoan) {
            return response()->json(['error' => 'Book not available'], Response::HTTP_CONFLICT);
        }
        $dueDate = Carbon::parse($bookLoan->due_date);

        $validatedData = [  
            'extended' => true,
            'extension_date' => $dueDate->addWeek()
        ]; 
    
        $book_loan = $bookLoan->update($validatedData);
    
        return response(['message' => 'Extended to '.$dueDate], Response::HTTP_OK);
    }
    public function Penalty(Request $request, $id)
    {
        $bookLoan = BookLoan::findOrFail($id); 
        if (!$bookLoan) {
            return response()->json(['error' => 'Book not available'], Response::HTTP_CONFLICT);
        }
        $dueDate = Carbon::parse($bookLoan->due_date);

        $validatedData = [  
            'penalty_amount' => 500,
            'penalty_status' => 'unpaid'
        ]; 
    
        $book_loan = $bookLoan->update($validatedData);
    
        return response(['message' => 'Penalty set'], Response::HTTP_OK);
    }
    public function payPenalty(Request $request, $id)
    {
        $bookLoan = BookLoan::findOrFail($id); 
        if (!$bookLoan) {
            return response()->json(['error' => 'Book not available'], Response::HTTP_CONFLICT);
        }
        $dueDate = Carbon::parse($bookLoan->due_date);

        $validatedData = [   
            'penalty_status' => 'paid'
        ]; 
    
        $book_loan = $bookLoan->update($validatedData);
    
        return response(['message' => 'Penalty paid'], Response::HTTP_OK);
    }
    public function approveBook(string $id)
    {
        $book = BookLoan::findOrFail($id);
        $book->due_date = Carbon::now()->addWeek();
        $book->status = 'borrowed';
        $book->save();
        return response(['message' => 'Book approved successfully'], Response::HTTP_OK); 
    }
    public function returnBook(string $id)
    {
        $book = BookLoan::findOrFail($id);
        $book->return_date = Carbon::now();
        $book->status = 'returned';
        $book->save();
        return response(['message' => 'Book returned successfully'], Response::HTTP_OK); 
    }
    public function update(Request $request, string $id)
    {
        $exists = BookLoan::where('name', $request->name)->where('isbn', $request->isbn)->where('id', '!=', $id)->first();
        if ($exists) {
            return response(['message' => 'Book with this info already exists'], Response::HTTP_CONFLICT);
        }
        $book = BookLoan::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required',
            'publisher' => 'required',
            'isbn' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'description' => 'required',
            'pages' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('public/images');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['added_by'] = Auth::id();
        $book->update($validatedData);

        return response(['message' => 'Book updated successfully'], Response::HTTP_OK);
    }


    public function destroy(string $id)
    {
        $book = BookLoan::findOrFail($id);
        $book->delete();

        return response(['message' => 'deleted successfully'], Response::HTTP_OK);
    }
}
