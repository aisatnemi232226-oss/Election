<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'positions';

    // Enable timestamps but only for created_at since updated_at doesn't exist
    public $timestamps = true;
    
    // Override the UPDATED_AT constant to null since the column doesn't exist
    const UPDATED_AT = null;

    // Tell SoftDeletes trait to use 'delete_at' instead of 'deleted_at'
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'name', 'description', 'status', 'tile_img',
    ];

    // Update the dates array to use the correct column names
    protected $dates = ['deleted_at', 'created_at'];

    /**
     * Retrieve the model for a bound value.
     * This allows route model binding to work with soft deleted records
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}