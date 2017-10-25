<?php

namespace Magnetar\Tariffs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Magnetar\Tariffs\Presenters\ValidatePresenter;

class UserObject extends Model {

    protected $table = 'magnetar_tariffs_user_objects';

    protected $rules = [
        'price' => 'integer',
        'object_id' => 'integer',
        'module_id' => 'required|integer',
        'user_id' => 'required|integer',
        'data' => 'required|json',
        'expired_at' => 'string',
        'paid_at' => 'string',
    ];

    use ValidatePresenter;

    public function getDataAttribute($value) {

        return $value = json_decode($value, true);

    }

    /**
     * Scope lj objects.
     *
     * @param object $query
     * @return object $query
     */
    public function scopeObjects($query) {

        return $query->leftJoin('magnetar_tariffs_objects', 'magnetar_tariffs_objects.id', '=', 'magnetar_tariffs_user_objects.object_id');

    }

}