<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'stock', 'reorder_point', 'price', 'category'];

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}