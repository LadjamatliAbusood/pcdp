<?php

namespace App\Http\Controllers;

use App\Models\ClientCasenoModel;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ClientInfoModel;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\DB;
use App\Models\ClientCategoryModel;
use Illuminate\Support\Facades\Log;
use App\Models\ClientIDPresentedModel;
use App\Models\IdentifyingInformation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class IdentifyingInformationController extends Controller
{
    private $stepRules = [
        // Step 0: Identifying Information
        0 => [
            'client_id' => ['nullable', 'exists:client_personal_info,id'],
            'case_no' => ['nullable', 'max:255'],
            'nickname' => ['nullable', 'max:255'],
            'firstname' => ['required', 'max:225'],
            'middlename' => ['nullable', 'max:225'],
            'lastname' => ['required', 'max:225'],
            'extensionname' => ['nullable', 'max:225'],
            'sex' => ['required', 'in:1,2'],
            'birthdate' => ['required', 'date'],
            'age' => ['nullable', 'max:225'],
            'birth_place' => ['required', 'max:225'],
            'birth_registered_with_local' => ['required', 'in:Yes,No'],
            'registered_with_local_where' => ['required_if:birth_registered_with_local,Yes', 'max:225'],
            'registered_with_local_reason_why' => ['required_if:birth_registered_with_local,No', 'max:225'],
            'civil_status' => ['required', 'max:225'],
            'religion' => ['required', 'max:225'],
            'dialect' => ['required', 'max:225'],
            'address_in_ph_region' => ['required', 'max:225'],
            'address_in_ph_province' => ['required', 'max:225'],
            'address_in_ph_city' => ['required', 'max:225'],
            'address_in_ph_brgy' => ['required', 'max:225'],
            'address_in_ph_street' => ['max:225'],
            'address_in_ph_house_no' => ['max:225'],
            'address_in_malaysia' => ['required', 'max:225'],
            'education_attainment' => ['required', 'in:1,2,3,4,5'],
            'eligibility' => ['max:225'],
            'eligibility_date_acquired' => ['nullable', 'required_with:eligibility', 'date'],
            'skills' => ['required', 'max:225'],
            'estimated_income_foriegn' => ['required', 'max:225'],
            'estimated_code_currency' => ['nullable', 'max:225'],
            'estimated_income_local' => ['required', 'max:225'],
            'estimated_code' => ['nullable', 'max:225'],
        ],
        // Step 1: Family Composition 
        1 => [
            'family_members' => ['nullable', 'array'],
            'family_members.*.fam_img' => ['file', 'nullable', 'max:5120'], // Changed to nullable file on update
            'family_members.*.nickname' => ['nullable', 'max:255'],
            'family_members.*.firstname' => ['required', 'max:225'],
            'family_members.*.middlename' => ['nullable', 'max:225'],
            'family_members.*.lastname' => ['required', 'max:225'],
            'family_members.*.extensionname' => ['nullable', 'max:225'],
            'family_members.*.sex' => ['required', 'in:1,2'],
            'family_members.*.birthdate' => ['required', 'date'],
            'family_members.*.civil_status' => ['required', 'max:225'],
            'family_members.*.relationship' => ['required', 'max:225'],
            'family_members.*.education_attainment' => ['required', 'in:1,2,3,4,5'],
            'family_members.*.skills_and_occupation' => ['required', 'max:225'],
            'family_members.*.estimated_income_foriegn' => ['nullable', 'max:225'],
            'family_members.*.estimated_code_currency' => ['nullable', 'max:225'],
            'family_members.*.estimated_income_local' => ['nullable', 'max:225'],
            'family_members.*.estimated_code' => ['nullable', 'max:225'],
            'family_members.*.health_status' => ['required', 'max:225'],
        ],

        2 => [

            'client_category_id' => ['required', 'exists:clients_category,id'],
            'typeofclient' => ['required', 'max:225'],
            'id_presented' => ['required', 'max:225'],
            'length_stay_in_malaysia' => ['required', 'max:225'],
            'length_stay_in_malaysia_options' => ['required', 'in:1,2,3,4'],
            'additional_length_option_if_with_years' => [
                'nullable',
                'required_if:length_stay_in_malaysia_options,4',
                'in:2,3',
            ],

            'length_value_if_with_years' => [
                'nullable',
                'required_if:length_stay_in_malaysia_options,4',
                'integer',
                'min:1',
            ],
            'client_went_malaysia' => ['required', 'max:225'],
            'client_employeed' => ['required', 'in:Yes,No'],
            'nature_of_work' => ['required_if:client_employeed,Yes', 'max:225'],
            'position_title' => ['required_if:client_employeed,Yes', 'max:225'],
            'name_and_address_of_employee' => ['required_if:client_employeed,Yes', 'max:225'],

            'valid_paper_type' => [
                'required_if:client_went_malaysia,With valid papers',
                'nullable',
                'max:225'
            ],
            'client_repatriated' => ['nullable', 'required_unless:client_went_malaysia,Victim of Traficking,Victim of Illegal Rectruitment', 'max:225'],
            'travel_document_no' => ['nullable', 'required_unless:client_went_malaysia,Victim of Traficking,Victim of Illegal Rectruitment', 'max:225'],
            'issued_by' => ['nullable', 'required_unless:client_went_malaysia,Victim of Traficking,Victim of Illegal Rectruitment', 'max:225'],
            'date_issued' => ['nullable', 'required_unless:client_went_malaysia,Victim of Traficking,Victim of Illegal Rectruitment', 'date'],
            'passport_ic_no' => ['nullable', 'required_unless:client_went_malaysia,Victim of Traficking,Victim of Illegal Rectruitment', 'max:225'],

            'client_problem' => ['required', 'max:225'],
            'client_plan' => ['required', 'in:return_to_malaysia,remain_in_the_ph'],
            'client_reason' => ['required_if:client_plan,return_to_malaysia', 'max:225'],
            'client_employment' => ['required_if:client_plan,remain_in_the_ph', 'max:225'],
            'contact_person_fullname' => ['required', 'max:225'],
            'contact_person_phonenumber' => ['required', 'numeric'],
            'contact_person_relationship' => ['required', 'max:225']
        ],

        3 => [
            'interventions_needed' => ['required', 'array'],
            'referred_to' => ['required', 'max:225']
        ],
    ];

    protected function getAllRules(): array
    {
        return array_merge(
            $this->stepRules[0],
            $this->stepRules[1],
            $this->stepRules[2],
            $this->stepRules[3],
        );
    }

    public function validateStep(Request $request, $step)
    {
        $stepIndex = (int) $step;

        if (!isset($this->stepRules[$stepIndex])) {
            throw ValidationException::withMessages([
                'step' => 'Invalid step index provided.',
            ]);
        }

        $rules = $this->stepRules[$stepIndex];
        $messages = [];
        $clientId = $request->input('client_id');
        $caseNo = $request->input('case_no');

        if ($stepIndex === 0) {
            $messages = [
                'estimated_income_foriegn.required' => 'The foreign field is required.',
                'estimated_income_local.required' => 'The local field is required.',
            ];

            if ($caseNo) {
                $uniqueRule = Rule::unique('client_caseno', 'case_no');


                if ($clientId) {

                    $uniqueRule->ignore($caseNo, 'case_no');
                }

                $rules['case_no'] = array_filter($rules['case_no'], fn($rule) => !str_contains($rule, 'unique'));
                $rules['case_no'][] = $uniqueRule;
            }
        }

        if ($stepIndex === 2) {
            $messages = [
                'valid_paper_type.required_if' => 'Select Types of with valid papers options.',
                'client_plan.required' => 'Please select the client\'s plan (Return or Remain).',
                'client_plan.in' => 'The selected client plan is invalid.',
                'client_reason.required_if' => 'The reason is required if the client plans to return to Malaysia.',
                'client_employment.required_if' => 'The reason is required if the client plans remain in the Philippines',
                'client_category_id.required' => 'The client category field is required.',
                'nature_of_work.required_if' => 'The Nature of Work is required when the client is employed (Yes).',
                'position_title.required_if' => 'The Position Title is required when the client is employed (Yes).',
                'name_and_address_of_employee.required_if' => 'The Employer Name and Address is required when the client is employed (Yes).',
            
                'additional_length_option_if_with_years.required_if' =>
                'Please select months or weeks when adding extra duration.',

            ];
        }


        $request->validate($rules, $messages);


        return redirect()->route('new-client', request()->only('prefill'))->with('success', 'Step validated');
    }


    public function store(Request $request)
    {
        $finalRules = $this->getAllRules();

        // Validate the request
        $validated = $request->validate($finalRules);

        $familyData = $validated['family_members'] ?? [];
        unset($validated['family_members']);

        $step0Keys = array_keys($this->stepRules[0]);
        $step2Keys = array_keys($this->stepRules[2]);
        $step3Keys = array_keys($this->stepRules[3]);

        $clientInfoData = collect($validated)->only($step0Keys)->toArray();
        unset($clientInfoData['client_id']); // ignore old client_id
        $caseNo = $clientInfoData['case_no'] ?? null;
        unset($clientInfoData['case_no']); // remove before create client

        $assessmentData = collect($validated)->only($step2Keys)->toArray();
        $servicesData = collect($validated)->only($step3Keys)->toArray();

        $clientCategoryId = $validated['client_category_id'] ?? null;

        try {
            DB::beginTransaction();

            // 1. Create new client (always new)
            $client = ClientInfoModel::create($clientInfoData);

            // 2. Create case, keep the given case_no from form
            $caseData = ['client_personal_info_id' => $client->id];
            if ($caseNo) {
                $caseData['case_no'] = $caseNo;
            }

            $case = $client->ClientCaseno()->create($caseData);

            // 3. Category case
            $categoryCase = $case->CategoryCase()->create([
                'client_category_id' => $clientCategoryId
            ]);

            // 4. Assessment
            $assessmentData['client_category_case_id'] = $categoryCase->id;
            $categoryCase->ClientAssessment()->create($assessmentData);

            // 5. Services
            $servicesData['client_category_case_id'] = $categoryCase->id;
            $categoryCase->ClientServices()->create($servicesData);

            // 6. Family members
            foreach ($familyData as $member) {
                $imagePath = null;
                if (isset($member['fam_img']) && $member['fam_img'] instanceof UploadedFile) {
                    $imagePath = $member['fam_img']->store('family_images', 'public');
                }

                $member['client_category_case_id'] = $categoryCase->id;
                $member['fam_img'] = $imagePath;

                $categoryCase->ClientFamilyMembers()->create($member);
            }

            DB::commit();

            return redirect()->route('client.show', $client->id)
                ->with('success', 'Client duplicated successfully with existing case_no.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving client data: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An internal error occurred during data saving.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }







    public function index() {}

    public function show($id)
    {
        $client = ClientInfoModel::with([
            'ClientCaseno.CategoryCase.ClientAssessment',
            'ClientCaseno.CategoryCase.ClientServices',
            'ClientCaseno.CategoryCase.ClientFamilyMembers'
        ])->findOrFail($id);

        return Inertia::render('Admin/NewProfile/Timeline/StoreFingerprint/ClientShow', [
            'client' => $client
        ]);
    }


    public function prefill()
    {
        $client = ClientInfoModel::latest()->with('ClientCaseno')->first();

        if (!$client) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $client->id,
            'firstname' => $client->firstname,
            'lastname' => $client->lastname,
            'birthdate' => $client->birthdate,
            'sex' => $client->sex,
            'client_caseno' => $client->clientCaseno
        ]);
    }

    public function edit(IdentifyingInformation $identifyingInformation)
    {
        //
    }

    public function update(Request $request, IdentifyingInformation $identifyingInformation)
    {
        //
    }

    public function destroy(IdentifyingInformation $identifyingInformation)
    {
        //
    }
}
