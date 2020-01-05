<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    /**
     * Causes the defined database fields (types, abilities, egg_groups, stats)
     * to automatically cast from JSON to a php array when the are retrieved.
     */
    protected $casts = [
        'types' => 'array',
        'abilities' => 'array',
        'egg_groups' => 'array',
        'stats' => 'array',
    ];

    // Fillable fields.
    protected $fillable = [
        'id', 'name', 'types', 'height', 
        'wieght', 'abilities', 'egg_groups', 
        'states', 'genus', 'description'
    ];
}
