<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Magnetar\Tariffs\Models\Module;
use Magnetar\Tariffs\Models\Object;
use Magnetar\Tariffs\Models\UserObject;
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\ResponseHelper;
use Magnetar\Tariffs\Services\ObjectServices;
use Magnetar\Tariffs\Services\UserBalanceService;
use DB;
use Magnetar\Tariffs\Services\UserObjectService;

class PaymentController extends Controller
{

    /**
     * Buy object.
     *
     * @param string $type
     * @param int $id
     * @return ResponseHelper
     */
    public function buyObject($type, $id) {

        $user_id = \Auth::guard('api')->user()->id;

        $object = Object::where('type_id', ObjectServices::getTypeId($type))->find($id);
        if(!$object)
            return ResponseHelper::response_error('not.found', 404);

        if(!is_array($object->data))
            return ResponseHelper::response_error('access.denied', 403);

        if($type == ObjectServices::MAGNETAR_TARIFFS_TARIFFS) {

            $user_tariff_check = UserObject::where('user_id', $user_id)->where('object_id', $id)->count();
            if ($user_tariff_check > 0)
                return ResponseHelper::response_error('tariff.exists', 403);

        }

        DB::beginTransaction();

        try {

            $object->addToUser($user_id);
            UserBalanceService::create($user_id, UserBalanceReference::BUY, $object->price, ['name' => $object->name]);

            DB::commit();
            return ResponseHelper::response_success('successful');

        } catch (\Exception $e) {

            DB::rollBack();
            return ResponseHelper::response_error($e->getMessage(), 403);

        }

    }

    /**
     * Buy module.
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function buyModule($id) {

        $user_id = \Auth::guard('api')->user()->id;

        $module = Module::find($id);
        if(!$module)
            return ResponseHelper::response_error('not.found', 404);

        if(!is_array($module->settings))
            return ResponseHelper::response_error('access.denied', 403);

        $ar_ids = [$id];
        if($module->group > 0) {

            $biggest_modules = Module::where('group', $module->group)->where('grade', '>', $module->grade)->pluck('id');
            foreach ($biggest_modules as $biggest_module)
                $ar_ids[] = $biggest_module;
        }

        $user_module_check = UserObject::where('user_id', $user_id)->whereIn('module_id', $ar_ids)->count();
        if ($user_module_check > 0)
            return ResponseHelper::response_error('tariff.exists', 403);

        DB::beginTransaction();

        try {

            $module->addToUser($user_id);
            UserBalanceService::create($user_id, UserBalanceReference::BUY, $module->price, ['name' => $module->name]);

            DB::commit();
            return ResponseHelper::response_success('successful');

        } catch (\Exception $e) {

            DB::rollBack();
            return ResponseHelper::response_error($e->getMessage(), 403);

        }

    }

    /**
     * Return all billings data of user.
     *
     * @param int $user_id
     * @return ResponseHelper
     */
    public function allInfo($user_id = null) {

        if($user_id == null)
            $user_id = \Auth::guard('api')->user()->id;

        $user_tariffs = UserObject::where('user_id', $user_id)->get();

        return ResponseHelper::response_success('successful', [
            'billing' => UserObjectService::allInfoProcess($user_tariffs),
            'balance' => UserBalanceService::currentBalance($user_id)
        ]);

    }

    /**
     * Decrease value test
     *
     * @return ResponseHelper
     */
    public function decreaseTest($type, $id) {

//        $mws = new \Magnetar\Tariffs\Services\Yandex\MWS(new \Magnetar\Tariffs\Services\Yandex\Settings());
//        $mws->repeatCardPayment('2000001627031', '134.0');
//echo 'qwe';die();
        UserObjectService::decreaseValue($id, \Auth::guard('api')->user()->id);
        return ResponseHelper::response_success('successful');

    }

    /**
     * Yandex callback.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function callback(Request $request) {

        return ResponseHelper::response_success('successful');

    }

}
