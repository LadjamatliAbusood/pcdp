<?php

namespace App\Http\Controllers;

use App\Models\ClientInfoModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientRecordsController extends Controller
{
  public function index()
    {
        // Load all related models
        $clients = ClientInfoModel::with([
            'ClientCaseno.CategoryCase.ClientCategory',
            'ClientCaseno.CategoryCase.ClientAssessment',
            'ClientCaseno.CategoryCase.ClientServices',
            'ClientCaseno.CategoryCase.ClientFamilyMembers',
        ])->get();

        // Flatten all data
        $records = $this->buildClientRecords($clients);

        return Inertia::render('Admin/ClientRecords/ClientRecords', [
            'title' => 'Client Records',
            'clients' => $records,
        ]);
    }

    /* ===========================
       Build all client cases
    =========================== */
    private function buildClientRecords($clients): array
    {
        $rows = [];

        foreach ($clients as $client) {
            foreach ($client->ClientCaseno as $caseno) {

                $caseKey = $caseno->case_no;
                $caseData = $this->buildCaseData($client, $caseno);

                if (!isset($rows[$caseKey])) {
                    $rows[$caseKey] = $caseData;
                } else {
                    $rows[$caseKey] = $this->mergeCaseData($rows[$caseKey], $caseData);
                }
            }
        }

        return collect($rows)
            ->sortByDesc('latest_date_raw')
            ->values()
            ->toArray();
    }

    /* ===========================
       Build a single case
       All data is kept as JSON
    =========================== */
    private function buildCaseData($client, $caseno): array
    {
        $categories = collect($caseno->CategoryCase)->map(function ($case) use ($client) {
            $assessment = $case->ClientAssessment;

            return [
               'client_info' => $client->toArray(),
                'category' => $case->ClientCategory?->category ?? null,
                'stay_duration' => $case->stay_duration ?? null,
                'assessment' => $assessment ? $assessment->toArray() : null,
                'services' => $case->ClientServices ? $case->ClientServices->toArray() : null,
                'family_members' => collect($case->ClientFamilyMembers)->map(fn ($f) => $f->toArray())->toArray(),
                'created_at' => $case->created_at,
            ];
        });

        return [
            'display_case_no' => $caseno->case_no,
            'category_count' => $categories->count(),
            'all_category_cases' => $categories->toArray(),
            'all_categories_names' => $categories->pluck('category')->unique()->values()->toArray(),
            'latest_date_raw' => $caseno->created_at,
            'raw_client_data' => $client->toArray(),
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
        }

        return $existing;
    }
}