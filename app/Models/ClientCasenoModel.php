<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientCasenoModel extends Model
{
     use HasFactory;
    protected $table = 'client_caseno';
    
    protected $fillable = [
        'client_personal_info_id',
        'case_no',
        'fingerprint_status',
        
    ];

protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        // If case_no is already provided from form → KEEP IT
        if (!empty($model->case_no)) {
            return;
        }

        $date = now()->format('m-d-Y');

        // Get latest case_no for today
        $latestCase = self::where('case_no', 'like', $date . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(case_no, '-', -1) AS UNSIGNED) DESC")
            ->first();

        if ($latestCase) {
            // Extract last number and increment
            $lastNumber = (int) substr($latestCase->case_no, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            // First entry of the day
            $nextNumber = 1;
        }

        $model->case_no = $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    });
}

    
    

    public function ClientInfo(){
       return $this->belongsTo(ClientInfoModel::class, 'client_personal_info_id');
    }

    public function CategoryCase(){
        return $this->hasMany(ClientCategoryCaseModel::class, 'client_caseno_id');
    }
}
