<?php

namespace App\Http\Controllers;

use App\Models\ClientIDPresentedModel;
use Illuminate\Http\Request;

class IDPresentedController extends Controller
{

public function index(Request $request) {
   $perPage = $request->input('per_page', 10);
    $search = $request->input('search'); 
    $query = ClientIDPresentedModel::orderBy('id', 'desc');
    $query->when($search, function ($q) use ($search) {
       
        $q->where('id_presented', 'like', "%{$search}%"); 
    
    });

    $categories = $query->paginate($perPage);
    return response()->json($categories);
}
       public function store(Request $request){
        //validate
        $fields = $request->validate([
            'id_presented' => 'required|string|max:255|unique:idpresented,id_presented',
            'status' => 'required|in:1,2',
        ]);

    $IDPresented = ClientIDPresentedModel::create($fields);
     return back()->with('success', 'ID Added successfully.');

    }

     public function update(Request $request, $id)
{
    $IDPresented = ClientIDPresentedModel::findOrFail($id);

    $fields = $request->validate([
        'id_presented' => 'required|string|max:255|unique:idpresented,id_presented,' . $id,
        'status' => 'required|in:1,2',
    ]);

    $IDPresented->update($fields);

    return back()->with('success', 'ID updated successfully.');
}


 public function getIDPresented()
    {
        $idpresented = ClientIDPresentedModel::where('status',1)->get();

        return response()->json([
        'success' => true,
        'idpresented' => $idpresented
    ]);
    }
}
