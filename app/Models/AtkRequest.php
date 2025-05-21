<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    protected $table = 'atk_requests';

    protected $fillable = ['user_id', 'notes', 'status'];

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