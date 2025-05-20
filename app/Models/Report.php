<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['title', 'description', 'report_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}