<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Magnetar\Tariffs\Models\Module;
use Magnetar\Tariffs\Models\Object;
use Magnetar\Tariffs\Models\UserObject;
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\ResponseHelper;
use Magnetar\Tariffs\References\ObjectReference;
use Magnetar\Tariffs\Services\UserBalanceService;
use DB;
use Magnetar\Tariffs\Services\UserObjectService;

class PaymentController extends Controller
{
    /**
     * Buy object.
     *
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return ResponseHelper
     */
    public function buyObject(Request $request, $type, $id) // TODO: посмотреть TODO
    {
        $user_id = \Auth::guard('api')->user()->id;

        $object = Object::where('type_id', ObjectReference::getTypeId($type))->find($id);
        if(!$object)
            return ResponseHelper::response_error('not.found', 404);

        if(!is_array($object->data))
            return ResponseHelper::response_error('access.denied', 403);

        if($type == ObjectReference::MAGNETAR_TARIFFS_TARIFFS) {
            $user_tariff_check = UserObject::where('user_id', $user_id)->where('object_id', $id)->count();
            if ($user_tariff_check > 0)
                return ResponseHelper::response_error('tariff.exists', 403);
        }

        DB::beginTransaction();
        try {
            $object->addToUser($user_id, $request->input('period', null));
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
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function buyModule(Request $request, $id)
    {
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
            return ResponseHelper::response_error('module.exists', 403);

        DB::beginTransaction();
        try {
            $module->addToUser($user_id, $request->input('period', null));
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
    public function allInfo($user_id = null)
    {
        if($user_id == null)
            $user_id = \Auth::guard('api')->user()->id;
        return ResponseHelper::response_success('successful', [
            'billing' => UserObjectService::allInfo($user_id),
            'balance' => UserBalanceService::currentBalance($user_id),
            'user_objects' => UserObject::where('user_id', $user_id)->get(),
        ]);
    }

    /**
     * Decrease value test.
     *
     * @return ResponseHelper
     */
    public function decreaseTest($type, $id)
    {
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
    public function callback(Request $request)
    {
        return ResponseHelper::response_success('successful');
    }
}
