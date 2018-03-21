<?php

namespace Magnetar\Tariffs\Services;

use Magnetar\Log\Services\LogServices;
use Magnetar\Tariffs\Models\Module;
use Magnetar\Tariffs\Models\Object;
use Magnetar\Tariffs\Models\UserObject;
use Magnetar\Tariffs\NumericHelper;
use Magnetar\Tariffs\References\UserBalanceReference;
use Carbon\Carbon;
use DB;

class UserObjectService
{

    /**
     * Decrease value, if isset count parameter.
     *
     * @param string $module_code
     * @param int $user_id
     * @return bool
     */
    public static function decreaseValue($module_code, $user_id, $decrease = 1)
    {
        $success_flag = false;
    
        $user_tariffs = UserObject::modules()
            ->where(UserObject::TABLE_NAME.'.user_id', $user_id)
            ->orWhere(Module::TABLE_NAME.'.code', $module_code)
            ->get();

        if(count($user_tariffs) == 0)
            return false;

        foreach ($user_tariffs as $user_tariff) {

            $data = $user_tariff->data;

            if (!isset($data['count']) || !isset($data['active']) || $data['active'] == false)
                continue;

            if ($data['count'] > $decrease || ($data['count'] == 1 && ($user_tariff->object_id != null))) {
                $data['count'] -= $decrease;
                $user_tariff->data = json_encode($data);
                $user_tariff->save();

                $success_flag = true;
                break;
            } else if ($data['count'] == $decrease) {
                $user_tariff->delete();
                $success_flag = true;
                break;
            }
        }
        return $success_flag;
    }

    /**
     * Preparing objects data.
     *
     * @param int $user_id
     * @return array
     */
    public static function allInfo($user_id)
    {
        $user_objects = UserObject::where('user_id', $user_id)->get();

        $ar_out = [];
        foreach ($user_objects as $item) {
            if (!isset($ar_out[$item->module_id])) {
                $ar_insert = [];
                foreach ($item->data as $k => $v)
                    if(in_array($k, ['active', 'count', 'customs']))
                        $ar_insert[$k] = $v;
                $ar_out[$item->module_id] = $ar_insert;
            } else if (isset($item->data['count'])) {
                if (!isset($ar_out[$item->module_id]['count']))
                    $ar_out[$item->module_id]['count'] = 0;

                $ar_out[$item->module_id]['count'] += $item->data['count'];
            }
        }
        return $ar_out;
    }

    /**
     * Return paid objects.
     *
     * @param integer $item_id
     * @param string $type
     * @param integer $user_id
     * @return array
     */
    public static function getPaidObjects($item_id, $type, $user_id)
    {
        switch ($type) {
            case 'object':
                $user_objects = UserObject::where('object_id', $item_id)->where('user_id', $user_id)->get();
                break;
            case 'module':
                $user_objects = UserObject::modules()->where(Module::TABLE_NAME.'.code', $item_id)->where('user_id', $user_id)->get();
                break;
            default:
                return [];
        }

        $out = [];
        foreach ($user_objects as $user_object)
            if(isset($user_object->data['active']) && $user_object->data['active'] == 1)
                $out[] = $user_object;

        return $out;
    }

    /**
     * Check expired object, buy if expired and enough money.
     *
     * @param integer $user_id
     */
    public static function checkExpired($user_id = null)
    {
        $user_objects = UserObject::where(function ($query) {
            $query->whereNotNull('object_id')->orWhereNotNull('module_id');
        })->where(function ($query) {
            $query->where(DB::raw("paid_at + INTERVAL '1' DAY"), '<', Carbon::now())
                ->orWhere('expired_at', '<', Carbon::now());
        })->when($user_id, function ($query) use ($user_id) {
            return $query->where('user_id', $user_id);
        })->get();

        $now = Carbon::now();

        $ar_objects = $ar_prices = $ar_prices_enabled = $ar_objects_ids = [];
        foreach ($user_objects as $object) {
            $ar_objects[$object->user_id][$object->object_id][] = $object;

            $price_insert = 0;
            if(new Carbon($object->expired_at) > $now || $object->expired_at == null)
                $price_insert += $object->price;

            $data = $object->data;
            if (isset($data['refresh_period']) && isset($data['refresh_in']) && new Carbon($data['refresh_in']) < $now && isset($data['base_price']))
                $price_insert += $data['base_price'];

            if(!isset($ar_prices[$object->user_id][$object->object_id])) {
                $ar_prices[$object->user_id][$object->object_id] = 0;
                $ar_prices_enabled[$object->user_id][$object->object_id] = 0;
            }

            $ar_prices[$object->user_id][$object->object_id] += $price_insert;

            if (isset($data['active']) && $data['active'] == true)
                $ar_prices_enabled[$object->user_id][$object->object_id] += $price_insert;

            if (!in_array($object->object_id, $ar_objects_ids))
                $ar_objects_ids[] = $object->object_id;
        }

        if(count($ar_objects_ids) > 0)
            $objects = Object::whereIn('id', $ar_objects_ids)->get()->keyBy('id');

        foreach ($ar_prices as $user_id => $user_prices) {

            $necessary_sum = array_sum($user_prices);
            $necessary_sum_enabled = array_sum($ar_prices_enabled[$user_id]);
            $user_balance = UserBalanceService::currentBalance($user_id);

            if ($necessary_sum > $user_balance)
                $user_balance = UserBalanceService::buyBalance($user_id, $necessary_sum - $user_balance);

            foreach ($user_prices as $object_id => $price) {
                if ($necessary_sum <= $user_balance) {
                    DB::beginTransaction();

                    $daily_price = 0;
                    foreach ($ar_objects[$user_id][$object_id] as &$user_object) {
//                        dd(new Carbon($object->expired_at));
//dd($user_object);
                        $module = Module::find($user_object->module_id);
                        if (new Carbon($user_object->expired_at) < $now) {
                            if(!$module || $module->price == null)
                                $user_object->delete();
                            else {
                                $data = $user_object['data'];
                                $settings = $module['settings'];

                                if(isset($settings['customs'])) {
                                    $data['customs'] = $settings['customs'];
                                    $user_object->data = json_encode($data);
                                }

                                $user_object->price = current($module->price)['price'];
                                $user_object->expired_at = null;
                                $user_object->object_id = null;

                                $user_object->save();
                            }
                        }

                        $pay_date = new Carbon($user_object->paid_at);
                        $pay_date->addDay();

                        $data = $user_object['data'];
                        $data['active'] = true;

                        if ($pay_date < $now) {
                            $daily_price += $user_object->price;
                            $user_object->paid_at = Carbon::now();
                            $user_object->data = json_encode($data);
                            $user_object->save();
                        }

                        if (isset($data['refresh_period']) && isset($data['refresh_in']) && new Carbon($data['refresh_in']) < $now && isset($data['base_price'])) {
                            $refresh_in = Carbon::now();
                            $data['refresh_in'] = $refresh_in->add(new \DateInterval($data['refresh_period']))->toIso8601String();

                            $user_object->data = json_encode($data);
                            $user_object->save();
                            UserBalanceService::create($user_id, UserBalanceReference::BUY, $data['base_price'], ['name' => $module->name]);
                        }
                    }

                    if($daily_price > 0)
                        UserBalanceService::create($user_id, UserBalanceReference::DAILY_BUY, $daily_price);

                    DB::commit();
                } else {
                    foreach ($ar_objects[$user_id][$object_id] as &$user_object) {
                        $data = $user_object['data'];
                        $data['active'] = false;
                        $user_object->data = json_encode($data);
                        $user_object->save();
                    }
                }
                unset($user_object);
            }

            if($necessary_sum_enabled > $user_balance) {
                $log_data = [
                    'text' => 'Платные услуги отключены, из-за отсутсвия баланса.',
                    'user_id' => $user_id
                ];

                $user_email = UserDataServices::getData($user_id, 'email');
                if($user_email != null)
                    $log_data['email'] = $user_email;

                $user_phone = UserDataServices::getData($user_id, 'phone');
                if($user_phone != null)
                    $log_data['phone'] = $user_phone;

                LogServices::send('user_notification', $log_data);
            }
        }
    }
    
    /**
     * Send notification to user, if isn't enough balance.
     *
     */
    public static function sendNotifications()
    {
        $user_objects = UserObject::get();
        $now = Carbon::now();
        $days_count = 3;

        $ar_prices = [];
        foreach ($user_objects as $object) {

            for($i = 1; $i <= $days_count; $i++)
                $price_insert[$i] = 0;

            if(new Carbon($object->expired_at) > $now || $object->expired_at == null) {
                for($i = 1; $i <= $days_count; $i++)
                    $price_insert[$i] += $object->price;
            }

            $data = $object->data;
            if (isset($data['refresh_period']) && isset($data['refresh_in']) && isset($data['base_price'])) {

                $refresh_in = new Carbon($data['refresh_in']);
                for($i = 1; $i <= $days_count; $i++) {

                    $refresh_current = clone $refresh_in;
                    while (true) {
                        if ($refresh_current < Carbon::now()->addDays($i - 1))
                            $refresh_current->add(new \DateInterval($data['refresh_period']));
                        else
                            break;
                    }

                    if (Carbon::now()->addDays($i - 1) < $refresh_current && $refresh_current < Carbon::now()->addDays($i))
                        $price_insert[$i] += $data['base_price'];
                }
            }

            if(!isset($ar_prices[$object->user_id]))
                for($i = 1; $i <= $days_count; $i++)
                    $ar_prices[$object->user_id][$i] = 0;

            for($i = 1; $i <= $days_count; $i++)
                $ar_prices[$object->user_id][$i] += $price_insert[$i];
        }

        foreach ($ar_prices as $user_id => $price) {

            $balance = UserBalanceService::currentBalance($user_id);
            for ($i = 1; $i <= $days_count; $i++) {

                $check_price = 0;
                for ($j = 1; $j <= $i; $j++)
                    $check_price += $price[$j];

                if($balance < $check_price) {
                    $log_data = [
                        'text' => 'Вашего баланса хватит на ' . $i . ' ' . NumericHelper::plural(['день', 'дня', 'дней'],  $i) . '.',
                        'user_id' => $user_id
                    ];

                    $user_email = UserDataServices::getData($user_id, 'email');
                    if($user_email != null)
                        $log_data['email'] = $user_email;

                    $user_phone = UserDataServices::getData($user_id, 'phone');
                    if($user_phone != null)
                        $log_data['phone'] = $user_phone;

                    LogServices::send('user_notification', $log_data);
                    break;
                }
            }
        }
    }

    /**
     * Calculate expired_at field.
     *
     * @param array $periods
     * @param string $request_period
     * @throws string
     */
    public static function calculateExpired_at($periods, $request_period)
    {
        if($periods != null) {
            if(!isset($request_period))
                throw new \Exception('not.found.period');

            $check_period = false;
            foreach ($periods as $period => $val) {
                if($request_period == $period) {
                    $check_period = true;
                    break;
                }
            }

            if($check_period == false)
                throw new \Exception('not.valid.period');

            $period = $request_period;
        } else {
            if(isset($request_period))
                $period = $request_period;
            else
                $period = 'P0Y';
        }

        $date = new Carbon();
        return $date->add(new \DateInterval($period));
    }
}