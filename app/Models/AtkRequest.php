<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    protected $table = 'atk_requests';

    protected $fillable = ['user_id', 'notes', 'status'];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Item melalui pivot table request_items
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'request_items', 'atk_request_id', 'item_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Relasi ke model RequestItem
     */
    public function requestItems()
    {
        return $this->hasMany(RequestItem::class, 'atk_request_id')->with('item');
    }
}