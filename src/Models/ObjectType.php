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
use Magnetar\Tariffs\Presenters\ValidatePresenter;

class ObjectType extends Model {

    protected $table = 'magnetar_tariffs_object_types';

    protected $rules = [
        'name' => 'required|string'
    ];

    use ValidatePresenter;

    protected $fillable = ['name'];

}