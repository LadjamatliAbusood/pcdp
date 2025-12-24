<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientIDPresentedModel extends Model
{
    use HasFactory;
     protected $table = 'idpresented';
 
   protected $fillable = [
    'id_presented',
    'status'];

    
}
