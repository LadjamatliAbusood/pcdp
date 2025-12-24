<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFingerprintModel extends Model
{

    use HasFactory;

    protected $table = 'client_fingerprints';

    protected $fillable = [
        'client_category_case_id',
        'remark',
       
        'left_thumb', 
        'left_index', 
        'left_middle',
        'left_ring',
        'left_pinky', 

        'right_thumb', 
        'right_index', 
        'right_middle',
        'right_ring',
        'right_pinky',          
    ];


     public function CategoryCase(){
       return $this->belongsTo(ClientCategoryCaseModel::class, 'client_category_case_id');
    }
}
