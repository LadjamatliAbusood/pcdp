<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCategoryModel extends Model
{
    use HasFactory;
    protected $table = 'clients_category';
 
   protected $fillable = [
    'category',
    'status'];

 public function CategoryCase()
{
    return $this->hasMany(
        ClientCategoryCaseModel::class,
        'client_category_id'
    );
}
}
