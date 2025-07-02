<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'atk_request_id',
        'item_id',
        'quantity',
        'status',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_READY_TO_PICKUP = 'ready_to_pickup';

    public function request()
    {
        return $this->belongsTo(AtkRequest::class, 'atk_request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
