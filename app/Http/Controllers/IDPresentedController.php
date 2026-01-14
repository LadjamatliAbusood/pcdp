<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ClientIDPresentedModel;

class IDPresentedController extends Controller
{


public function index(Request $request)
{
    $search = $request->input('search');
    
    $perPage = $request->input('per_page', 10); 

    $clients = ClientIDPresentedModel::when($search, function ($q) use ($search) {
            $q->where('id_presented', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage) 
        ->withQueryString();

    return Inertia::render('Superadmin/ClientCategory/IDPresented', [ 
        'clients' => $clients,
        'filters' => [
            'search' => $search,
            'per_page' => $perPage 
        ],
        'title' => 'Client Identification Card'
    ]);
}

    public function store(Request $request)
    {
        $fields = $request->validate([
            'id_presented' => 'required|string|max:255|unique:idpresented,id_presented',
            'status' => 'required|in:1,2',
        ]);

        ClientIDPresentedModel::create($fields);
        return back();
    }

    public function update(Request $request, $id)
    {
        $client = ClientIDPresentedModel::findOrFail($id);
        $fields = $request->validate([
            'id_presented' => 'required|string|max:255|unique:idpresented,id_presented,' . $id,
            'status' => 'required|in:1,2',
        ]);

        $client->update($fields);
        return back();
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
