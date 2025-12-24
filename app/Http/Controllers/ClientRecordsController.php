<?php

namespace App\Http\Controllers;

use App\Models\ClientInfoModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientRecordsController extends Controller
{
    public function index(Request $request){
     

      $clients = ClientInfoModel::with(['ClientCaseno.CategoryCase.ClientAssessment',
                                     'ClientCaseno.CategoryCase.ClientServices',
                                     'ClientCaseno.CategoryCase.ClientFamilyMembers',
                                      'ClientCaseno.CategoryCase.ClientCategory',
                                    
                                  
    ])->get();
        
   
      return Inertia::render('Admin/ClientRecords/ClientRecords', [
        'title' => 'Client Records',
        'clients' => $clients
    ]);


    }
}
