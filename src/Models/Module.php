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
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\Services\UserBalanceService;
use Magnetar\Tariffs\Services\UserObjectService;
use Carbon\Carbon;
use DB;

class Module extends Model {

    const TABLE_NAME = 'magnetar_tariffs_modules';
    protected $table = self::TABLE_NAME;

    protected $fillable = ['group', 'grade', 'name', 'settings', 'price', 'code'];

    protected $rules = [
        'name' => 'required|string',
        'price' => 'required|json',
        'settings' => 'required|json',
        'group' => 'integer',
        'grade' => 'integer',
    ];

    use ValidatePresenter;

    public function getPriceAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Delete smaller module of user.
     *
     * @param int $user_id
     */
    public function deleteSmallerTariffs($user_id)
    {
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
     * @throws
     */
    public function addToUser($user_id, $period)
    {
        if($this->price == null)
            throw new \Exception('access.denied');

        $expired_at = UserObjectService::calculateExpired_at($this->price, $period);
        $user_object = new UserObject();
        $user_object->price = current($this->price)['price'];

        foreach ($this->price as $interval => $item) {
            $date = new Carbon();
            $date_check = $date->add(new \DateInterval($interval));

            if($date_check <= $this->expired_at)
                $user_object->price = $item['price'];
            else
                break;
        }

        $user_object->module_id = $this->id;
        $user_object->user_id = $user_id;
        $user_object->data = json_encode($this->settings);
        $user_object->expired_at = $expired_at;
        $user_object->paid_at = Carbon::now();

        $this->deleteSmallerTariffs($user_id);
        $user_object->save();
        UserBalanceService::create($user_id, UserBalanceReference::DAILY_BUY, $user_object->price);
    }
}