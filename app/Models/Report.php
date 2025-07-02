<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    const STATUS_DIKIRIM = 'dikirim';
    const STATUS_DITERIMA = 'diterima';

    protected $fillable = ['user_id', 'title', 'description', 'file_path', 'report_date', 'status'];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}