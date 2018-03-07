<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Magnetar\Tariffs\Models\ObjectType;
use Magnetar\Tariffs\ResponseHelper;

class ObjectTypeCrudController extends Controller
{

    /**
     * Return modules list.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function index(Request $request)
    {
        $out['object_types'] = ObjectType::get();
        return ResponseHelper::response_success("successful", $out);
    }

    /**
     * Return module by id.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function show(Request $request, $id)
    {
        $out['object_type'] = ObjectType::find($id);

        if(!$out['object_type'])
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
    public function process(Request $request, $id = null)
    {
        if($id == null)
            $object_type = new ObjectType();
        else {
            $object_type = ObjectType::find($id);

            if(!$object_type)
                return ResponseHelper::response_error("not.found", 404);
        }

        if ($object_type->validate($request->all())) {
            $object_type->name = $request->input('name');
            $object_type->save();
            return ResponseHelper::response_success("update", ['object_type' => $object_type]);
        } else
            return ResponseHelper::response_error($object_type->errors(), 400);
    }

    /**
     * Delete module.
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function destroy($id)
    {
        $object_type = ObjectType::find($id);

        if(!$object_type)
            return ResponseHelper::response_error("not.found", 404);

        $object_type->delete();
        return ResponseHelper::response_success("successful");
    }

}