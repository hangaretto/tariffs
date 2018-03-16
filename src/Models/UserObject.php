<?php

namespace Magnetar\Tariffs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Magnetar\Tariffs\Presenters\ValidatePresenter;

class UserObject extends Model {

    const TABLE_NAME = 'magnetar_tariffs_user_objects';
    protected $table = self::TABLE_NAME;

    protected $fillable = ['price', 'object_id', 'user_id', 'module_id', 'data', 'expired_at', 'paid_at'];

    protected $rules = [
        'price' => 'numeric',
        'object_id' => 'integer',
        'module_id' => 'required|integer',
        'user_id' => 'required|integer',
        'data' => 'required|json',
        'expired_at' => 'string',
        'paid_at' => 'string',
    ];

    use ValidatePresenter;

    public function getDataAttribute($value)
    {
        return $value = json_decode($value, true);
    }

    /**
     * Scope lj objects.
     *
     * @param object $query
     * @return object $query
     */
    public function scopeObjects($query)
    {
        return $query->leftJoin(Object::TABLE_NAME, Object::TABLE_NAME.'.id', '=', self::TABLE_NAME.'.object_id');
    }

    /**
     * Scope lj modules.
     *
     * @param object $query
     * @return object $query
     */
    public function scopeModules($query)
    {
        return $query->leftJoin(Module::TABLE_NAME, Module::TABLE_NAME.'.id', '=', self::TABLE_NAME.'.module_id');
    }

}