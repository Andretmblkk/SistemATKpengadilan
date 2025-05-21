<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
class Request extends Model
{
    protected $fillable = ['user_id', 'item_id', 'quantity', 'status'];
    protected $attributes = [
        'status' => 'pending',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public static function create(array $attributes = [])
    {
        if (!isset($attributes['user_id']) && auth()->check()) {
            $attributes['user_id'] = auth()->id();
        }
        Log::info('Attempt to create Request: ', $attributes);
        return parent::create($attributes);
    }
}