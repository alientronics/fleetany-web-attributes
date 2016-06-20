<?php namespace App\Entities;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Value extends BaseModel {

    /**
     * Generated
     */

    use SoftDeletes;

    protected $table = 'values';
    protected $fillable = ['entity_key', 'entity_id', 'attribute_id', 'value'];


    public function key() {
        return $this->belongsTo(\App\Entities\Key::class, 'attribute_id', 'id');
    }
    
    public static function boot()
    {
        parent::boot();
        Value::creating(function ($value) {
            $value->company_id = ( $value->company_id ?: Auth::user()['company_id'] );
        });
    }


}
