<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFamInfoModel extends Model
{
    use HasFactory;
    protected $table = 'client_fam_info';

    protected $fillable = [
        'client_category_case_id',
        'fam_img',
        'nickname', 
        'firstname', 
        'middlename',
        'lastname',
        'extensionname', 
        'sex', 
        'birthdate',
        'civil_status',
        'relationship', 
        'education_attainment', 
        'skills_and_occupation',
        'health_status', 
        'estimated_income_foriegn', 
        'estimated_code_currency',
        'estimated_income_local', 
        'estimated_code',
    ];
    protected $casts = [
        'skills_and_occupation' => 'array',
        'birthdate' => 'date',
    ];

    public function CategoryCase(){
        return $this->belongsTo(ClientCategoryCaseModel::class, 'client_category_case_id');
    }
}
