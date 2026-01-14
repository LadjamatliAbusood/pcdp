<?php

namespace App\Http\Controllers;

use App\Models\ClientCategoryModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientCategoryController extends Controller
{
    
public function index(Request $request)
{
    $search = $request->input('search');
    
    $perPage = $request->input('per_page', 10); 

    $clients = ClientCategoryModel::when($search, function ($q) use ($search) {
            $q->where('category', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage) 
        ->withQueryString();

    return Inertia::render('Superadmin/ClientCategory/Category', [ 
        'clients' => $clients,
        'filters' => [
            'search' => $search,
            'per_page' => $perPage 
        ],
        'title' => 'Client Categories'
    ]);
}

    public function store(Request $request)
    {
        $fields = $request->validate([
            'category' => 'required|string|max:255|unique:clients_category,category',
            'status' => 'required|in:1,2',
        ]);

        ClientCategoryModel::create($fields);
        return back();
    }

    public function update(Request $request, $id)
    {
        $client = ClientCategoryModel::findOrFail($id);
        $fields = $request->validate([
            'category' => 'required|string|max:255|unique:clients_category,category,' . $id,
            'status' => 'required|in:1,2',
        ]);

        $client->update($fields);
        return back();
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
