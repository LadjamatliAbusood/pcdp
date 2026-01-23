<?php

namespace App\Http\Controllers;

use App\Models\ClientInfoModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
class ClientRecordsController extends Controller
{
  public function index(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $search  = $request->input('search');
    $page    = $request->input('page', 1);

    /**
     * STEP 1: Determine matching CASE NUMBERS
     * (search decides WHICH CASES, not which rows)
     */
    $matchingCaseNos = null;

    if ($search) {
        $matchingCaseNos = ClientInfoModel::where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->orWhereHas('ClientCaseno', function ($q) use ($search) {
                $q->where('case_no', 'like', "%{$search}%");
            })
            ->with('ClientCaseno')
            ->get()
            ->flatMap(fn ($client) => $client->ClientCaseno->pluck('case_no'))
            ->unique()
            ->values();
    }

    /**
     * STEP 2: Load ALL CLIENTS under those CASE NUMBERS
     */
    $query = ClientInfoModel::with([
        'ClientCaseno.CategoryCase.ClientCategory',
        'ClientCaseno.CategoryCase.ClientAssessment',
        'ClientCaseno.CategoryCase.ClientServices',
        'ClientCaseno.CategoryCase.ClientFamilyMembers',
    ]);

    if ($search && $matchingCaseNos && $matchingCaseNos->isNotEmpty()) {
        $query->whereHas('ClientCaseno', function ($q) use ($matchingCaseNos) {
            $q->whereIn('case_no', $matchingCaseNos);
        });
    }

    /**
     * STEP 3: Build grouped records (unchanged logic)
     */
    $clients = $query->get();
    $records = $this->buildClientRecords($clients);

    /**
     * STEP 4: Manual pagination (kept as-is)
     */
    $paginatedRecords = new LengthAwarePaginator(
        collect($records)->forPage($page, $perPage)->values(),
        count($records),
        $perPage,
        $page,
        [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]
    );

    return Inertia::render('Admin/ClientRecords/ClientRecords', [
        'title'   => 'All Client Records',
        'clients' => $paginatedRecords,
        'filters' => [
            'search'   => $search,
            'per_page' => $perPage,
        ],
    ]);
}



      private function buildClientRecords($clients): array
    {
        $rows = [];

        foreach ($clients as $client) {
            foreach ($client->ClientCaseno as $caseno) {
                $caseKey = $caseno->case_no;
                $caseData = $this->buildCaseData($client, $caseno);

                $rows[$caseKey] = $rows[$caseKey] ?? [];
                $rows[$caseKey] = $rows[$caseKey] ? $this->mergeCaseData($rows[$caseKey], $caseData) : $caseData;
            }
        }

        return collect($rows)
            ->sortByDesc('latest_date_raw')
            ->values()
            ->toArray();
    }

  private function buildCaseData($client, $caseno): array
{
    $latestCategoryCase = collect($caseno->CategoryCase)
        ->sortByDesc('created_at')
        ->first();

    $categories = collect($caseno->CategoryCase)->map(function ($case) use ($client) {

        $rawCategory = $case->ClientCategory?->category ?? 'N/A';
        $categoryName = $rawCategory; 

        $assessment = $case->ClientAssessment;
        
       
      if (str_contains(strtolower($rawCategory), 'other')) {
    $customValue = $assessment->other_category ?? null;
 
    if (!empty($customValue)) {
        $categoryName = $customValue; 
    }

}

        return [
            'client_info' => $client->toArray(),
            'category' => $categoryName, 
            'stay_duration' => $case->stay_duration,
            'assessment' => $assessment ? $assessment->toArray() : null, // Ensure this isn't null
            'services' => $case->ClientServices?->toArray(),
            'family_members' => collect($case->ClientFamilyMembers)->map(fn ($f) => $f->toArray())->toArray(),
            'created_at' => $case->created_at,
        ];
    });

    return [
        'display_case_no' => $caseno->case_no,
        'category_count' => $categories->count(),
        'all_category_cases' => $categories->toArray(),
        'all_categories_names' => $categories->pluck('category')->unique()->values()->toArray(),
        'latest_client_info' => $client->toArray(),
        'latest_date_raw' => optional($latestCategoryCase)->created_at,
    ];
}

    private function mergeCaseData(array $existing, array $incoming): array
    {
        $existing['category_count'] += $incoming['category_count'];
        $existing['all_category_cases'] = array_merge($existing['all_category_cases'], $incoming['all_category_cases']);
        $existing['all_categories_names'] = collect(array_merge($existing['all_categories_names'], $incoming['all_categories_names']))->unique()->values()->toArray();

        if (strtotime($incoming['latest_date_raw']) > strtotime($existing['latest_date_raw'])) {
            $existing['latest_date_raw'] = $incoming['latest_date_raw'];
            $existing['latest_client_info'] = $incoming['latest_client_info'];
        }

        return $existing;
    }
}