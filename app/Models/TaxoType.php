<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxoType extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxo_type_name',
        'taxo_type_description',
    ];

    protected $table = 'taxo_types';

    public function taxoLists()
    {
        return $this->hasMany(TaxoList::class, 'taxonomy_type', 'id');
    }
}

