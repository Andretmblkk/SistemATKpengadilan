<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'atk_requests'; // Tentukan tabel secara eksplisit

    protected $fillable = ['user_id', 'status', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'request_items', 'atk_request_id', 'item_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}