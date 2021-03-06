<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 22.09.17
 * Time: 15:14
 */

namespace Magnetar\Tariffs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class UserData extends Model {

    protected $table = 'magnetar_tariffs_user_data';

    protected $fillable = ['user_id', 'data'];

    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

}