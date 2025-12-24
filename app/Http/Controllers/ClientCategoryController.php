<?php

namespace App\Http\Controllers;

use App\Models\ClientCategoryModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientCategoryController extends Controller
{
    

 public function index(Request $request) {
   $perPage = $request->input('per_page', 10);
    $search = $request->input('search'); 
    $query = ClientCategoryModel::orderBy('id', 'desc');
    $query->when($search, function ($q) use ($search) {
       
        $q->where('category', 'like', "%{$search}%"); 
    
    });

    $categories = $query->paginate($perPage);
    return response()->json($categories);
}

    public function store(Request $request){
        //validate
        $fields = $request->validate([
            'category' => 'required|string|max:255|unique:clients_category,category',
            'status' => 'required|in:1,2',
        ]);

    $category = ClientCategoryModel::create($fields);
     return back()->with('success', 'Category Added successfully.');

    }
 public function update(Request $request, $id)
{
    $category = ClientCategoryModel::findOrFail($id);

    $fields = $request->validate([
        'category' => 'required|string|max:255|unique:clients_category,category,' . $id,
        'status' => 'required|in:1,2',
    ]);

    $category->update($fields);

    return back()->with('success', 'Category updated successfully.');
}



 public function getCategories()
    {
        $categories = ClientCategoryModel::where('status',1)->get();

        return response()->json([
        'success' => true,
        'categories' => $categories
    ]);
    }
}
