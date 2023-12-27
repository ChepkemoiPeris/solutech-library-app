<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;



class BooksController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query();

        $page = $query['page'] ?? 1;
        $limit = $query['limit'] ?? 10;

        $searchTerm = $query['name'] ?? null;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $queryBuilder = Books::with(['bookLoan']);

        if ($searchTerm) {
            $queryBuilder->where('name', 'like', "%{$searchTerm}%");
        }

        $books = $queryBuilder->paginate($limit);
        $books->getCollection()->transform(function ($book) {
            $book->image_url = Storage::url($book->image);
            return $book;
        });
        $books->getCollection()->transform(function ($book) {
            $book->isBorrowed = $this->isBookBorrowed($book->bookLoan);
            return $book;
        });

        return response()->json($books);
    }

    protected function isBookBorrowed($bookLoan)
    {
        foreach ($bookLoan as $loan) {
            switch ($loan->status) {
                case 'borrowed':
                case 'pending':
                    return true;
                case 'returned':
                    return false;
                default:
                    return false;
            }
        }

        return false;
    }
    public function store(Request $request)
    {
        $exists = Books::where('name', $request->name)->where('isbn', $request->isbn)->first();
        if ($exists) {
            return response(['message' => 'Book with this info already exists'], Response::HTTP_CONFLICT);
        }
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
        $book = Books::create($validatedData);

        return response(['message' => 'Book added successfully'], Response::HTTP_OK);
    }

    public function getBookDetails(string $id)
    {
        $book = Books::with('bookLoan')->findOrFail($id); 
        $book->image_url = Storage::url($book->image);    
        return response()->json($book);
    }
    public function update(Request $request, string $id)
    {
        $book = Books::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required',
            'publisher' => 'required',
            'isbn' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'description' => 'required',
            'pages' => 'required'
        ]);
        if ($request->hasFile('image')) {
            Storage::delete($book->image);
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
        $book = Books::findOrFail($id);
        Storage::delete($book->image);
        $book->delete();

        return response(['message' => 'deleted successfully'], Response::HTTP_OK);
    }
}
