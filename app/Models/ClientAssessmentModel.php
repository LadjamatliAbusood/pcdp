<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssessmentModel extends Model
{
    
    use HasFactory;
 protected $table = 'client_assessment';

    protected $fillable = [
        'client_category_case_id',
        'typeofclient',
        'id_presented',
        'length_stay_in_malaysia',
        'length_stay_in_malaysia_options',
        'additional_length_option_if_with_years',
        'length_value_if_with_years',
        'client_went_malaysia',
        'valid_paper_type',
        'client_employeed',
        'nature_of_work', 
        'position_title', 
        'name_and_address_of_employee',
        'client_repatriated',
        'travel_document_no', 
        'issued_by', 'date_issued',
        'passport_ic_no', 
        'client_problem', 
        'client_plan', 
        'client_reason',
        'client_employment', 
        'contact_person_fullname', 
        'contact_person_phonenumber',
        'contact_person_relationship',
    ];
    
    protected $casts = [
        'date_issued' => 'date',
    ];

    public function CategoryCase(){
       return $this->belongsTo(ClientCategoryCaseModel::class, 'client_category_case_id');
    }
}
