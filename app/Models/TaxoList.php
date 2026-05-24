<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxoList extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent',
        'taxonomy_ref_key',
        'taxonomy_name',
        'taxonomy_description',
        'taxonomy_slug',
        'taxonomy_type',
        'taxonomy_image',
        'taxonomy_sort',
        'taxonomy_status',
    ];

    public function taxoType()
    {
        return $this->belongsTo(TaxoType::class, 'taxonomy_type', 'id');
    }

    public function taxoParent()
    {
        return $this->belongsTo(TaxoList::class, 'parent', 'id');
    }

    public function taxoChild()
    {
        return $this->hasMany(TaxoList::class, 'parent', 'id');
    }

    public function taxoChild_publics()
    {
        return $this->hasMany(TaxoList::class, 'parent', 'id')->where('taxonomy_status','ACTIVE')->orderBy('taxonomy_sort','asc');
    }
}

