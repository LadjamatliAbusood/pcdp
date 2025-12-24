<?php

namespace App\Models;

use App\Models\ClientFingerprintModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientCategoryCaseModel extends Model
{
     use HasFactory;

    protected $table = 'client_category_case';

    protected $fillable = ['client_caseno_id', 'client_category_id'];


        // Relationships (One-to-Many/One-to-One)

          public function ClientCaseno(){
        return $this->belongsTo(ClientCasenoModel::class,'client_caseno_id');
    }
    public function ClientCategory(){
        return $this->belongsTo(ClientCategoryModel::class,'client_category_id');
    }
    public function ClientFamilyMembers(){
        return $this->hasMany(ClientFamInfoModel::class, 'client_category_case_id');
    }
    public function ClientAssessment(){
        return $this->hasOne(ClientAssessmentModel::class, 'client_category_case_id');
    }
    public function ClientServices(){
        return $this->hasOne(ClientServicesModel::class, 'client_category_case_id');
    }
  
    public function Fingerprint(){
        return $this->hasOne(ClientFingerprintModel::class,'client_category_case_id');
    }

}
