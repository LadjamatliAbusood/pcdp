<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class user_infos extends Model
{
  use HasFactory;

    protected $fillable = [
        'user_id',

        'nickname',

        'first_name',
        'middle_name',
        'last_name',
        'ext_name',

        'contact_number',
        'position',

        // 'desgination_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
