<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = ['name', 'description', 'stock', 'price', 'supplier_id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function atkRequests()
    {
        return $this->belongsToMany(AtkRequest::class, 'request_items', 'item_id', 'atk_request_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->name)) {
                Log::error('Attempt to create Item without name: ' . json_encode($item->toArray()) . ' | Stack trace: ' . (new \Exception)->getTraceAsString());
                throw new \Exception('Field "name" is required for Item.');
            }
        });
    }
}