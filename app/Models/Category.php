<?
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Category extends Model
{
    use HasRoles; // Integrasi Spatie untuk izin
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'description', 'created_at', 'updated_at'
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'id');
    }
}