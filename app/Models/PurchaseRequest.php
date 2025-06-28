<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'item_id',
        'current_stock',
        'reorder_point',
        'requested_quantity',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
} 