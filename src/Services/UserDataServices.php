<?php

namespace Magnetar\Tariffs\Services;

use Magnetar\Tariffs\Models\UserData;

class UserDataServices
{
    private static $ar_data = [];

    /**
     * Set user data.
     *
     * @param int $user_id
     * @param array $data
     * @return void
     */
    public static function setData($user_id, $data)
    {
        $obj_user_data = UserData::select('data')->where('user_id', $user_id)->first();

        if(!$obj_user_data) {
            $user_data = [];
            $obj_user_data = new UserData();
            $obj_user_data->user_id = $user_id;
        } else
            $user_data = $obj_user_data->data;

        foreach ($data as $k => $v)
            $user_data[$k] = $v;

        self::$ar_data[$user_id] = $user_data;

        $obj_user_data->data = json_encode($user_data);
        $obj_user_data->save();
    }

    /**
     * Get data by key
     *
     * @param int $user_id
     * @param string $key
     * @return string|int
     */
    public static function getData($user_id, $key)
    {
        if(isset(self::$ar_data[$user_id]))
            $user_data = self::$ar_data[$user_id];
        else {
            $obj_user_data = UserData::select('data')->where('user_id', $user_id)->first();
            $user_data = $obj_user_data->data;
            self::$ar_data[$user_id] = $user_data;
        }

        if(!isset($user_data) || !isset($user_data[$key]))
            return null;
        else
            return $user_data[$key];
    }
}