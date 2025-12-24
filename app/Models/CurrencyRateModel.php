<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRateModel extends Model
{
    use HasFactory;
    protected $table = 'currency_rates';

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'rate_date',
    ];
}
