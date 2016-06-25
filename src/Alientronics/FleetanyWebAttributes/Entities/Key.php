<?php namespace App\Entities;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Key extends BaseModel
{

    /**
     * Generated
     */

    use SoftDeletes;
    
    protected $table = 'keys';
    protected $fillable = ['entity_key', 'description', 'type', 'options'];


    public function values()
    {
        return $this->hasMany(\App\Entities\Value::class, 'attribute_id', 'id');
    }
    
    public static function boot()
    {
        parent::boot();
        Key::creating(function ($key) {
            $key->company_id = ( $key->company_id ?: Auth::user()['company_id'] );
        });
    }
}
