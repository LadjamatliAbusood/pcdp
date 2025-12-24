<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInfoModel extends Model
{
    use HasFactory;

    protected $table = 'client_personal_info';

    // All fields from Step 0, ensuring array/json fields are cast
    protected $fillable = [
        
        'nickname', 
        'firstname', 
        'middlename', 
        'lastname', 
        'extensionname',
        'sex', 
        'birthdate',
        'age',  
        'birth_place', 
        'birth_registered_with_local',
        'registered_with_local_where', 
        'registered_with_local_reason_why',
        'civil_status', 
        'religion', 
        'dialect',
        'address_in_ph_region',
        'address_in_ph_province', 
        'address_in_ph_city', 
        'address_in_ph_brgy',
        'address_in_ph_street', 
        'address_in_ph_house_no', 
        'address_in_malaysia',
        'education_attainment', 
        'eligibility', 
        'eligibility_date_acquired',
        'skills', 
        'estimated_income_foriegn', 
        'estimated_code_currency',
        'estimated_income_local', 
        'estimated_code',
    ];

    protected $casts = [
      
        'birthdate' => 'date',
        'eligibility_date_acquired' => 'date',
    ];

    // Relationships (One-to-Many/One-to-One)
  
    public function ClientCaseno(){
        return $this->hasMany(ClientCasenoModel::class,'client_personal_info_id');
    }



 
        

    
}
