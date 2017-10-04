<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Magnetar\Tariffs\Models\Module;
use Magnetar\Tariffs\ResponseHelper;

class ModuleCrudController extends Controller
{

    /**
     * Return modules list.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function index(Request $request) {

        $out['modules'] = Module::get();

        return ResponseHelper::response_success("successful", $out);

    }

    /**
     * Return module by id.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function show(Request $request, $id) {

        $out['module'] = Module::find($id);

        if(!$out['module'])
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
    public function process(Request $request, $id = null) {

        if($id == null)
            $module = new Module();
        else {

            $module = Module::find($id);

            if(!$module)
                return ResponseHelper::response_error("not.found", 404);

        }

        if ($module->validate($request->all())) {

            $module->group = $request->input('group');
            $module->grade = $request->input('grade');
            $module->name = $request->input('name');
            $module->settings = $request->input('settings');
            $module->price = $request->input('price');
            $module->currency_id = $request->input('currency_id');

            $module->save();

            return ResponseHelper::response_success("update", ['module' => $module]);

        } else
            return ResponseHelper::response_error($module->errors(), 400);

    }

    /**
     * Delete module.
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function destroy($id) {

        $module = Module::find($id);

        if(!$module)
            return ResponseHelper::response_error("not.found", 404);

        $module->delete();

        return ResponseHelper::response_success("successful");

    }

}