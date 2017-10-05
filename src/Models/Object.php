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

class Object extends Model {

    protected $table = 'magnetar_tariffs_objects';

    protected $rules = [
        'name' => 'required|string',
        'type_id' => 'required|integer',
        'currency_id' => 'integer',
        'price' => 'required|numeric',
        'data' => 'required|string',
    ];

    use ValidatePresenter;

    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Add object to user.
     *
     * @param int $user_id
     */
    public function addToUser($user_id) {

        foreach ($this->data as $module_key => $data) {

            $module_check = Module::find($module_key);
            if(!$module_check)
                continue;

            $check_settings = json_decode($module_check->settings, true);

            if(!isset($check_settings['count']) || (isset($check_settings['count']) && $check_settings['count'] == 0)) {

                $ar_ids = [$module_key];
                if ($module_check->group > 0) {
                    $biggest_modules = Module::where('group', $module_check->group)->where('grade', '>', $module_check->grade)->pluck('id');
                    foreach ($biggest_modules as $biggest_module)
                        $ar_ids[] = $biggest_module;
                }

                $user_module_check = UserObject::where('user_id', $user_id)->whereIn('module_id', $ar_ids)->count();
                if ($user_module_check > 0)
                    continue;

            }
// TODO: start, удаляем дубли, возможно нужно смотреть по expired_at
            $module_check = Module::select('magnetar_tariffs_modules.settings')
                ->leftJoin('magnetar_tariffs_user_objects', 'magnetar_tariffs_modules.id', '=', 'magnetar_tariffs_user_objects.module_id')
                ->where('magnetar_tariffs_user_objects.user_id', $user_id)
                ->where('magnetar_tariffs_user_objects.module_id', $module_key)
                ->first();
// TODO: end
            if(isset($module_check)) {
                $check_settings = json_decode($module_check->settings, true);
                if(!isset($check_settings['count']) || (isset($check_settings['count']) && $check_settings['count'] == 0))
                    continue;
            }

            $user_tariff = new UserObject();

            if(isset($data['period']) && isset($data['period_type']) && in_array($data['period_type'], ['day', 'week', 'month', 'year'])) {

                $current = Carbon::now();
                switch ($data['period_type']) {
                    case 'day':
                        $user_tariff->expired_at = $current->addDays($data['period']);
                        break;
                    case 'week':
                        $user_tariff->expired_at = $current->addWeeks($data['period']);
                        break;
                    case 'month':
                        $user_tariff->expired_at = $current->addMonths($data['period']);
                        break;
                    case 'year':
                        $user_tariff->expired_at = $current->addYears($data['period']);
                        break;
                }

                $user_tariff->period = $data['period'];
                $user_tariff->period_type = $data['period_type'];

            } else if(isset($data['period']))
                continue;

            $user_tariff->object_id = $this->id;
            $user_tariff->user_id = $user_id;
            $user_tariff->price = $this->price;

            $module = Module::find($module_key);

            if(!$module)
                continue;

            $setting_arr = json_decode($module->settings, true);
            if(isset($setting_arr['count']) && $setting_arr['count'] == 1 && isset($data['count']) && $data['count'] > 0) {

                $setting_arr['count'] = $data['count'];
                $module->settings = json_encode($setting_arr);

            } else if(isset($setting_arr['count']))
                continue;

            $user_tariff->module_id = $module->id;
            $user_tariff->data = $module->settings;

            $module->deleteSmallerTariffs($user_id);

            $user_tariff->save();

        }

    }

}