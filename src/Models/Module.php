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

class Module extends Model {

    protected $table = 'magnetar_tariffs_modules';

    /**
     * Delete smaller module of user.
     *
     * @param int $user_id
     */
    public function deleteSmallerTariffs($user_id) {

        if($this->grade > 0) {

            $deleted_user_modules = self::select('id')
                ->where('grade', '<', $this->grade)->where('group', $this->group)->get();

            $ar_deleted_user_modules = [];
            foreach ($deleted_user_modules as $deleted_user_module)
                $ar_deleted_user_modules[] = $deleted_user_module->id;

            if(count($ar_deleted_user_modules) > 0)
                UserObject::whereIn('module_id', $ar_deleted_user_modules)
                    ->where('user_id', $user_id)->delete();

        }

    }

    /**
     * Add module to user.
     *
     * @param int $user_id
     */
    public function addToUser($user_id) {

        $user_tariff = new UserObject();

        $user_tariff->module_id = $this->id;
        $user_tariff->user_id = $user_id;
        $user_tariff->price = $this->price;
        $user_tariff->data = json_encode($this->settings);

        $this->deleteSmallerTariffs($user_id);

        $user_tariff->save();

    }

    public function getSettingsAttribute($value) {

        return $value = json_decode($value, true);

    }

}