<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Magnetar\Tariffs\Models\UserCard;
use Magnetar\Tariffs\Models\UserObject;
use Magnetar\Tariffs\ResponseHelper;
use Magnetar\Tariffs\References\ObjectReference;

class UserObjectCrudController extends Controller
{

    /**
     * Return modules list.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function index(Request $request)
    {
        $route_name = \Request::route()->getName();

        if($route_name == 'user_objects.list.admin') {

            if($request->has('user_id'))
                $out['user_objects'] = UserObject::where('user_id', $request->input('user_id'))->get();
            else
                $out['user_objects'] = UserObject::get();

        } else if($route_name == 'user_objects.list.public')
            $out['user_objects'] = UserObject::where('user_id', \Auth::guard('api')->user()->id)->get();
        else
            return ResponseHelper::response_error('access.denied', 403);

        return ResponseHelper::response_success("successful", $out);
    }

    /**
     * Return module by id.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function show(Request $request, $id = null)
    {
        $out['user_object'] = UserObject::find($id);

        if(!$out['user_object'])
            return ResponseHelper::response_error("not.found", 404);

        return ResponseHelper::response_success("successful", $out);
    }

    /**
     * Create/update module.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function process(Request $request, $id)
    {
        if($id == null)
            $user_object = new UserObject();
        else {
            $user_object = UserObject::find($id);

            if(!$user_object)
                return ResponseHelper::response_error("not.found", 404);
        }

        if ($user_object->validate($request->all())) {
            $user_object->object_id = $request->input('object_id');
            $user_object->module_id = $request->input('module_id');
            $user_object->user_id = $request->input('user_id');
            $user_object->data = $request->input('data');
            $user_object->price = $request->input('price');
            $user_object->expired_at = $request->input('expired_at');
//            $user_object->paid_at = $request->input('paid_at');

            $user_object->save();
        } else
            return ResponseHelper::response_error($user_object->errors(), 400);
    }

    /**
     * Delete module.
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function destroy($id)
    {
        $user_object = UserObject::find($id);

        if(!$user_object)
            return ResponseHelper::response_error("not.found", 404);

        $user_object->delete();
        return ResponseHelper::response_success("successful");
    }

    /**
     * Deleting object of user.
     *
     * @param string $type
     * @param int $id
     * @param int $user_id
     * @return ResponseHelper
     */
    public function destroyObject($type, $id, $user_id = null)
    {
        if($user_id == null)
            $user_id = \Auth::guard('api')->user()->id;

        $user_object = UserObject::objects()->where('type_id', ObjectReference::getTypeId($type))
            ->where('user_id', $user_id)->where('object_id', $id)->get();

        if(count($user_object) == 0)
            return ResponseHelper::response_error("not.found", 404);

        UserObject::objects()->where('type_id', ObjectReference::getTypeId($type))
            ->where('user_id', $user_id)->where('object_id', $id)->delete();

        return ResponseHelper::response_success("successful");
    }
}