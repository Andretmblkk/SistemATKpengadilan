<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class, 'supplier_id');
    }
}