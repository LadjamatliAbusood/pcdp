<?php

namespace App\Http\Controllers;

use App\Models\ClientCasenoModel;
use App\Models\ClientFingerprintModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use App\Models\ClientCategoryCaseModel;

class FingerprintController extends Controller
{
    // Define the fingerprint columns once
    private $fingerprintColumns = [
        'left_thumb', 'left_index', 'left_middle', 'left_ring', 'left_pinky',
        'right_thumb', 'right_index', 'right_middle', 'right_ring', 'right_pinky',
    ];

public function index()
{
    try {
        // Fetch all fingerprints with their CategoryCase -> ClientCaseno -> ClientInfo
        $records = ClientFingerprintModel::with('CategoryCase.ClientCaseno.ClientInfo')->get();

      $records = $records->map(function ($record) {

    $record->fingerprint_id = $record->id;

    $categoryCase = $record->CategoryCase;

    if ($categoryCase) {
        $record->client_category_case_id = $categoryCase->id;

        $clientInfo = $categoryCase->ClientCaseno->ClientInfo ?? null;
        if ($clientInfo) {
            // Only assign non-fingerprint fields dynamically
            foreach ($clientInfo->getAttributes() as $key => $value) {
                if (!in_array($key, $this->fingerprintColumns)) {
                    $record->$key = $value;
                }
            }

            $record->case_no = $categoryCase->ClientCaseno->case_no ?? null;
        }
    }

    // Base64 encode fingerprint templates
    foreach ($this->fingerprintColumns as $column) {
        if (!empty($record->$column)) {
            $record->$column = base64_encode((string)$record->$column);
        }
    }

    return $record;
});


        return response()->json($records);

    } catch (\Throwable $th) {
        return response()->json(['error' => 'Failed to retrieve records.'], 500);
    }
}








public function store(Request $request)
{
   
    $validationRules = [
        'client_category_case_id' => ['required', 'integer', 'exists:client_category_case,id'],
        'remark' => ['nullable', 'string', 'max:1000'],
    ];

    foreach ($this->fingerprintColumns as $column) {
        $validationRules[$column] = ['nullable', 'string'];
    }

    $validatedData = $request->validate($validationRules);

   
    foreach ($this->fingerprintColumns as $column) {
        if (!empty($validatedData[$column])) {
            $decoded = base64_decode($validatedData[$column], true);
            $validatedData[$column] = $decoded === false ? null : $decoded;
        }
    }

 
    DB::beginTransaction();
    try {
        $fingerprintRecord = ClientFingerprintModel::create($validatedData);

        $categoryCase = ClientCategoryCaseModel::find($fingerprintRecord->client_category_case_id);
        if ($categoryCase) {
            $caseNo  = $categoryCase->ClientCaseno;
            if ($caseNo) {
                $caseNo->fingerprint_status = 'Success';
                $caseNo->save();
            }
         
        }

        DB::commit();


      
        return response()->json([
            'message' => 'Fingerprint data saved successfully.',
            'success' => true,
            'id' => $fingerprintRecord->id
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();

      
        return response()->json([
            'error' => 'A server error occurred while saving the fingerprint data.',
            'message' => $e->getMessage()
        ], 500);
    }
}



}
