<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'file_path', 'report_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}