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
        $search = $request->input('search');
        $page = $request->input('page', 1);

        // Base query with relations
        $query = ClientInfoModel::with([
            'ClientCaseno.CategoryCase.ClientCategory',
            'ClientCaseno.CategoryCase.ClientAssessment',
            'ClientCaseno.CategoryCase.ClientServices',
            'ClientCaseno.CategoryCase.ClientFamilyMembers',
        ]);

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                  ->orWhere('lastname', 'like', "%$search%")
                  ->orWhereHas('ClientCaseno', fn($q2) => $q2->where('case_no', 'like', "%$search%"));
            });
        }

      
        $clients = $query->get();

     
        $records = $this->buildClientRecords($clients);

      
        $paginatedRecords = new LengthAwarePaginator(
            collect($records)->forPage($page, $perPage)->values(), 
            count($records), 
            $perPage,       
            $page,          
            [
                'path' => $request->url(),
                'query' => $request->query(), 
            ]
        );

        // Send to Inertia
        return Inertia::render('Admin/ClientRecords/ClientRecords', [
            'title' => 'All Client Records',
            'clients' => $paginatedRecords,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage
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

        $categories = collect($caseno->CategoryCase)->map(fn($case) => [
            'client_info' => $client->toArray(),
            'category' => $case->ClientCategory?->category ?? null,
            'stay_duration' => $case->stay_duration ?? null,
            'assessment' => $case->ClientAssessment?->toArray(),
            'services' => $case->ClientServices?->toArray(),
            'family_members' => collect($case->ClientFamilyMembers)->map(fn($f)=>$f->toArray())->toArray(),
            'created_at' => $case->created_at,
        ]);

        return [
            'display_case_no' => $caseno->case_no,
            'category_count' => $categories->count(),
            'all_category_cases' => $categories->toArray(),
            'all_categories_names' => $categories->pluck('category')->unique()->values()->toArray(),
            'latest_client_info' => $client->toArray(),
            'latest_date_raw' => optional($latestCategoryCase)->created_at,
            'raw_client_data' => $client->toArray(),
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