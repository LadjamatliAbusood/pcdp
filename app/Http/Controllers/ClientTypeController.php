<?php

namespace App\Http\Controllers;

use App\Models\ClientTypeModel;
use Illuminate\Http\Request;

class ClientTypeController extends Controller
{
    public function index(Request $request) {
   $perPage = $request->input('per_page', 10);
    $search = $request->input('search'); 
    $query = ClientTypeModel::orderBy('id', 'desc');
    $query->when($search, function ($q) use ($search) {
       
        $q->where('typeofclient', 'like', "%{$search}%"); 
    
    });

    $categories = $query->paginate($perPage);
    return response()->json($categories);
}
       public function store(Request $request){
        //validate
        $fields = $request->validate([
            'typeofclient' => 'required|string|max:255|unique:client_type,typeofclient',
            'status' => 'required|in:1,2',
        ]);

    $IDPresented = ClientTypeModel::create($fields);
     return back()->with('success', 'ID Added successfully.');

    }

     public function update(Request $request, $id)
{
    $IDPresented = ClientTypeModel::findOrFail($id);

    $fields = $request->validate([
        'typeofclient' => 'required|string|max:255|unique:client_type,typeofclient,' . $id,
        'status' => 'required|in:1,2',
    ]);

    $IDPresented->update($fields);

    return back()->with('success', 'Client updated successfully.');
}


 public function getClienttype()
    {
        $clienttype = ClientTypeModel::where('status',1)->get();

        return response()->json([
        'success' => true,
        'clienttype' => $clienttype
    ]);
    }
}
