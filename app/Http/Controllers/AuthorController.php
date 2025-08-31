<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
// use Request;
use \App\Traits\ApiResponser;
use \App\Models\Author;
use Illuminate\Http\Request; 
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // Get all authors
    public function index()
    {
        $authors = Author::all();
        return $this->successResponse($authors);

    }

    // Get a single author
    public function show($author)
    {
        $author = Author::findOrFail($author);
        return $this->successResponse($author);
    }

    // Create a new author
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'gender' => 'required|in:male,female',
            'country' => 'required|max:255'
        ]);
        $author = Author::create($request->all());
        return $this->successResponse($author, Response::HTTP_CREATED);
    }

    // Update an author
    public function update(Request $request, $author)
    {   
        $this->validate($request, [
            'name' => 'sometimes|required|max:255',
            'gender' => 'sometimes|required|in:male,female',
            'country' => 'sometimes|required|max:255'
        ]);
        $author = Author::findOrFail($author);
        $author->fill($request->all());
        if($author->isClean()){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $author->save();
        return $this->successResponse($author);
    }

    // Delete an author
    public function destroy($author)
    {
        $author = Author::findOrFail($author);
        // if (!$author) {
        //     return $this->errorResponse(['error' => 'Author not found'], 404);
        // }
        $author->delete();
        return $this->successResponse(['message' => 'Author deleted']);
    }

}
