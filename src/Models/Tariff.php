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
use Carbon\Carbon;
use Magnetar\Tariffs\Presenters\ValidatePresenter;
use Magnetar\Tariffs\References\ObjectReference;
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\Services\UserBalanceService;
use DB;
use Magnetar\Tariffs\Services\UserObjectService;

class Tariff extends Model {

    const TABLE_NAME = 'magnetar_tariffs_objects';
    protected $table = self::TABLE_NAME;

    protected $fillable = ['name', 'type_id', 'periods', 'data', 'code'];

    protected $rules = [
        'name' => 'required|string',
        'type_id' => 'required|integer',
        'data' => 'required|json',
        'periods' => 'json',
    ];

    use ValidatePresenter;

    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getPeriodsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Add object to user.
     *
     * @param int $user_id
     */
    public function addToUser($user_id, $period) {

        $expired_at = UserObjectService::calculateExpired_at($this->periods, $period);

        $daily_price = 0;
        foreach ($this->data as $module_key => $data) {

            $module_check = Module::find($module_key);
            if(!$module_check)
                continue;

            $check_settings = $module_check->settings;
            if(!isset($check_settings['count']) || (isset($check_settings['count']) && $check_settings['count'] == 0)) {

                if($this->type_id == ObjectReference::getTypeId(ObjectReference::MAGNETAR_TARIFFS_TARIFFS)) { // todo::
                    $user_module_check = UserObject::where('user_id', $user_id)->where('module_id', $module_key)->first();

                    if (isset($user_module_check))
                        $user_module_check->delete();
                }

                $ar_ids = [];
                if ($module_check->group > 0) {
                    $biggest_modules = Module::where('group', $module_check->group)->where('grade', '>', $module_check->grade)->pluck('id');
                    foreach ($biggest_modules as $biggest_module)
                        $ar_ids[] = $biggest_module;
                }

                if(count($ar_ids) > 0) {
                    $user_biggest_module_check = UserObject::where('user_id', $user_id)->whereIn('module_id', $ar_ids)->get();
                    if ($user_biggest_module_check > 0)
                        continue;
                }
            }
//// TODO: start, удаляем дубли, возможно нужно смотреть по expired_at
//            $module_check = Module::select(Module::TABLE_NAME.'.settings')
//                ->leftJoin(UserObject::TABLE_NAME, Module::TABLE_NAME.'.id', '=', UserObject::TABLE_NAME.'.module_id')
//                ->where(UserObject::TABLE_NAME.'.user_id', $user_id)
//                ->where(UserObject::TABLE_NAME.'.module_id', $module_key)
//                ->first();
//// TODO: end
//            if(isset($module_check)) {
//                $check_settings = $module_check->settings;
//                if(!isset($check_settings['count']) || (isset($check_settings['count']) && $check_settings['count'] == 0))
//                    continue;
//            }

            $user_tariff = new UserObject();

            if(isset($data['price']) && is_array($data['price'])) {
                $user_tariff->price = current($data['price'])['price'];
                foreach ($data['price'] as $interval => $price) {
                    $date = new Carbon();
                    $date_check = $date->add(new \DateInterval($interval));

                    if($date_check <= $expired_at)
                        $user_tariff->price = $price['price'];
                    else
                        break;
                }
            }

            $insert_data = ['active' => true];
            $user_tariff->user_id = $user_id;
            $module = Module::find($module_key);

            if(!$module)
                continue;

            $setting_arr = $module->settings;

            if(!isset($setting_arr['count']))
                $user_tariff->object_id = $this->id;

            if(isset($setting_arr['count']) && $setting_arr['count'] == 1 && isset($data['count']))
                $insert_data['count'] = $data['count'];
            else if(isset($setting_arr['count']) && $setting_arr['count'] == 1)
                continue;

            if(isset($data['refresh_period']) && $data['refresh_period']) {
                $insert_data['refresh_period'] = $data['refresh_period'];
                $date = new Carbon();
                $insert_data['refresh_in'] = $date->add(new \DateInterval($data['refresh_period']))->toIso8601String();
            }

            if(isset($data['customs']))
                $insert_data['customs'] = $data['customs'];

            if(isset($data['base_price'])) {
                $insert_data['base_price'] = $data['base_price'];
                UserBalanceService::create($user_id, UserBalanceReference::BUY, $data['base_price'], ['name' => $module->name]);
            }

            $user_tariff->module_id = $module->id;
            $user_tariff->data = json_encode($insert_data);

            $module->deleteSmallerTariffs($user_id);
            $user_tariff->paid_at = Carbon::now();
            $user_tariff->expired_at = $expired_at;
            $user_tariff->save();

            if(isset($user_tariff->price) && $user_tariff->price > 0)
                $daily_price += $user_tariff->price;
        }

        if($daily_price > 0)
            UserBalanceService::create($user_id, UserBalanceReference::DAILY_BUY, $daily_price);
    }
}