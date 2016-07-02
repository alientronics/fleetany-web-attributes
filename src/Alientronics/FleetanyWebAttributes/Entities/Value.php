<?php namespace Alientronics\FleetanyWebAttributes\Entities;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Value extends BaseModel
{

    /**
     * Generated
     */

    use SoftDeletes;

    protected $table = 'values';
    protected $fillable = ['entity_key', 'entity_id', 'attribute_id', 'value'];


    public function key()
    {
        return $this->belongsTo(\Alientronics\FleetanyWebAttributes\Entities\Key::class, 'attribute_id', 'id');
    }
    
}
