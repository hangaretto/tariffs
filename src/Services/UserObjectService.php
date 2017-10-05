<?php

namespace Magnetar\Tariffs\Services;

use Magnetar\Tariffs\Models\UserObject;

class UserObjectService
{

    /**
     * Decrease value, if isset count parameter.
     *
     * @param string $module_id
     * @param int $user_id
     * @return bool
     */
    public static function decreaseValue($module_id, $user_id) {

        $success_flag = false;
    
        $user_tariffs = UserObject::where('user_id', $user_id)
//            ->whereRaw("jsonb_exists(data, 'count')")
            ->where('module_id', $module_id)
            ->get();

        if(count($user_tariffs) == 0)
            return false;

        foreach ($user_tariffs as $user_tariff) {

            $data = $user_tariff->data;

            if(!isset($data['count']))
                continue;

            if($data['count'] > 1 || ($data['count'] == 1 && ($user_tariff->object_id != null))) {

                $data['count'] -= 1;
                $user_tariff->data = json_encode($data);
                $user_tariff->save();

                $success_flag = true;
                break;

            } else if($data['count'] == 1) {

                if($user_tariff->object_id == null && $user_tariff->module_id == null)
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
     * @param array $user_tariffs
     * @return array
     */
    public static function allInfoProcess($user_tariffs) {

        $ar_out = [];
        foreach ($user_tariffs as $item) {

            if($item->module_id != null)
                $ar_out[$item->module_id] = $item->data;
            else {

                foreach ($item->data as $k_d => $v_d) {

                    if(isset($ar_out[$k_d]))
                        $ar_out[$k_d]['count'] += $v_d['count'];
                    else
                        $ar_out[$k_d]['count'] = $v_d['count'];

                }

            }

        }

        return $ar_out;

    }

    /**
     * Return payment status.
     *
     * @param integer $item_id
     * @param string $type
     * @param integer $user_id
     * @return bool
     */
    public static function getPaymentStatus($item_id, $type, $user_id) {

        switch ($type) {
            case 'object':
                $user_objects = UserObject::where('object_id', $item_id)->where('user_id', $user_id)->get();
                break;
            case 'module':
                $user_objects = UserObject::where('module_id', $item_id)->where('user_id', $user_id)->get();
                break;
            default:
                return false;
        }

        foreach ($user_objects as $user_object)
            if(isset($user_object->data['active']) && $user_object->data['active'] == 1)
                return true;

        return false;

    }

}