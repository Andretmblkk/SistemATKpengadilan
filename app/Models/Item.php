<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'stock', 'price', 'supplier_id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requests()
    {
        return $this->belongsToMany(Request::class, 'request_items', 'item_id', 'request_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}