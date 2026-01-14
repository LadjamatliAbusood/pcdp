<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ClientTypeModel;

class ClientTypeController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');
    
    $perPage = $request->input('per_page', 10); 

    $clients = ClientTypeModel::when($search, function ($q) use ($search) {
            $q->where('typeofclient', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage) 
        ->withQueryString();

    return Inertia::render('Superadmin/ClientCategory/TypeofClient', [ 
        'clients' => $clients,
        'filters' => [
            'search' => $search,
            'per_page' => $perPage 
        ],
        'title' => 'Client Type'
    ]);
}

    public function store(Request $request)
    {
        $fields = $request->validate([
            'typeofclient' => 'required|string|max:255|unique:client_type,typeofclient',
            'status' => 'required|in:1,2',
        ]);

        ClientTypeModel::create($fields);
        return back();
    }

    public function update(Request $request, $id)
    {
        $client = ClientTypeModel::findOrFail($id);
        $fields = $request->validate([
            'typeofclient' => 'required|string|max:255|unique:client_type,typeofclient,' . $id,
            'status' => 'required|in:1,2',
        ]);

        $client->update($fields);
        return back();
    }

    public function getClienttype()
    {
        return response()->json([
            'success' => true,
            'clienttype' => ClientTypeModel::where('status', 1)->get(),
        ]);
    }
}
