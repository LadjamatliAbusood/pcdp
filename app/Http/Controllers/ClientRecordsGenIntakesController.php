<?php

namespace App\Http\Controllers;

use App\Models\ClientCategoryModel;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ClientInfoModel;
use Illuminate\Pagination\LengthAwarePaginator;
class ClientRecordsGenIntakesController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 10);
    $search    = $request->input('search');
$categories = $request->input('categories');
if (empty($categories)) {
    $categories = [];
} elseif (!is_array($categories)) {
    $categories = [$categories];
}

    // 1️⃣ Build query
    $query = ClientInfoModel::with([
        'ClientCaseno.CategoryCase.ClientCategory',
        'ClientCaseno.CategoryCase.ClientAssessment',
        'ClientCaseno.CategoryCase.ClientServices',
        'ClientCaseno.CategoryCase.ClientFamilyMembers',
    ])
    ->when($search, function ($q) use ($search) {
        $q->where(function ($qq) use ($search) {
            $qq->where('firstname', 'like', "%{$search}%")
               ->orWhere('middlename', 'like', "%{$search}%")
               ->orWhere('lastname', 'like', "%{$search}%")
               ->orWhereHas('ClientCaseno', fn ($q2) =>
                    $q2->where('case_no', 'like', "%{$search}%")
               )
               ->orWhereRaw(
                    "DATE_FORMAT(created_at, '%M %e, %Y') LIKE ?",
                    ["%{$search}%"]
               );
        });
    })
    // ✅ CATEGORY FILTER — THIS IS THE KEY FIX
    ->when(!empty($categories), function ($q) use ($categories) {
        $q->whereHas(
            'ClientCaseno.CategoryCase.ClientCategory',
            function ($qc) use ($categories) {
                $qc->whereIn('category', $categories)
                   ->where('status', 1);
            }
        );
    })
    ->orderBy('id', 'desc');

    // 2️⃣ Paginate AFTER filters
    $paginatedClients = $query
        ->paginate($perPage)
        ->withQueryString();

    // 3️⃣ Build records from paginated items
    $records = $this->buildClientRecords($paginatedClients->items());

    // 4️⃣ Rebuild paginator
    $paginator = new LengthAwarePaginator(
        $records,
        $paginatedClients->total(),
        $paginatedClients->perPage(),
        $paginatedClients->currentPage(),
        [
            'path'  => $paginatedClients->path(),
            'query' => $request->query(),
        ]
    );

    // 5️⃣ ALL categories for MultiSelect
    $allCategories = ClientCategoryModel::query()
        ->where('status', 1)
        
        ->orderBy('category')
        ->distinct()
        ->pluck('category')
        ->map(fn ($c) => [
            'label' => $c,
            'value' => $c,
        ])
        ->values();

    return Inertia::render('Admin/ClientRecords/ClientRecordsGenIntakes', [
        'title' => 'All General Intakes Records',
        'clients' => $paginator,
        'filters' => [
            'search' => $search,
            'per_page' => $perPage,
            'categories' => $categories, // ✅ persist
        ],
        'searchTerm' => $search,
        'categories' => $allCategories,
        
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
                $rows[$caseKey] = $rows[$caseKey]
                    ? $this->mergeCaseData($rows[$caseKey], $caseData)
                    : $caseData;
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
        $existing['all_category_cases'] = array_merge(
            $existing['all_category_cases'],
            $incoming['all_category_cases']
        );

        $existing['all_categories_names'] = collect(
            array_merge($existing['all_categories_names'], $incoming['all_categories_names'])
        )->unique()->values()->toArray();

        if (strtotime($incoming['latest_date_raw']) > strtotime($existing['latest_date_raw'])) {
            $existing['latest_date_raw'] = $incoming['latest_date_raw'];
            $existing['latest_client_info'] = $incoming['latest_client_info'];
        }

        return $existing;
    }
}
