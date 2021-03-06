<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Magnetar\Tariffs\Models\Module;
use Magnetar\Tariffs\Models\Tariff;
use Magnetar\Tariffs\ResponseHelper;
use Magnetar\Tariffs\References\ObjectReference;
use Validator;

class ObjectCrudController extends Controller
{
    /**
     * List of objects.
     *
     * @param Request $request
     * @param string $type
     * @return ResponseHelper
     */
    public function index(Request $request, $type)
    {
        $objects = Tariff::where('type_id', ObjectReference::getTypeId($type))->get();
        $modules = Module::get()->keyBy('id');
        foreach ($objects as $k => $object) {
            $out['objects'][$k] = $object;
            $object_modules = [];
            foreach ($object->data as $id => $item) {
                if(isset($modules[$id]))
                    $object_modules[$id] = $modules[$id];
            }
            $out['objects'][$k]['modules'] = $object_modules;
        }
        return ResponseHelper::response_success("successful", $out);
    }

    /**
     * Get object by id.
     *
     * @param string $type
     * @param int $id
     * @return ResponseHelper
     */
    public function show($type, $id)
    {
        $out['object'] = Tariff::where('type_id', ObjectReference::getTypeId($type))->find($id);

        if(!$out['object'])
            return ResponseHelper::response_error("not.found", 404);

        return ResponseHelper::response_success("successful", $out);
    }

    /**
     * Create/update object.
     *
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return ResponseHelper
     */
    public function process(Request $request, $type, $id = null)
    {
        if($id == null)
            $object = new Tariff();
        else {
            $object = Tariff::where('type_id', ObjectReference::getTypeId($type))->find($id);

            if(!$object)
                return ResponseHelper::response_error("not.found", 404);
        }

        if ($object->validate($request->all())) {
            $req_json = json_decode($request->input('data'), true);

            foreach ($req_json as $module_id => $item) {
                $module = Module::find($module_id);
                if(!$module)
                    unset($req_json[$module_id]);

                $module_settings = $module->settings;

                if(isset($item['count']) && !isset($module_settings['count']))
                    unset($req_json[$module_id]);
            }

            $object->name = $request->input('name');
            $object->type_id = $request->input('type_id');
            $object->periods = $request->input('periods');
            $object->data = json_encode($req_json);

// {"1": {"active": "true"}, "2": {"count": 50, "active": "true"}, "3": {"active": "true", "period": 10, "period_type": "day"}}
            $object->save();
            return ResponseHelper::response_success("update", ['object' => $object]);
        } else
            return ResponseHelper::response_error($object->errors(), 400);
    }

    /**
     * Delete object.
     *
     * @param string $type
     * @param int $id
     * @return ResponseHelper
     */
    public function destroy($type, $id)
    {
        $object = Tariff::where('type_id', ObjectReference::getTypeId($type))->find($id);

        if(!$object)
            return ResponseHelper::response_error("not.found", 404);

        $object->delete();
        return ResponseHelper::response_success("successful");
    }
}
