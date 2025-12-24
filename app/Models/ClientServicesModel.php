<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientServicesModel extends Model
{
    use HasFactory;
    protected $table = 'client_services_assistance';
    
    protected $fillable = [
        'client_category_case_id', 
        'interventions_needed', 
        'referred_to',
    ];
    
    protected $casts = [
        'interventions_needed' => 'array',
      
    ];

    public function CategoryCase(){
       return $this->belongsTo(ClientCategoryCaseModel::class, 'client_category_case_id');
    }
}
