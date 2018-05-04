<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Magnetar\Tariffs\ResponseHelper;
use DB;
use Magnetar\Tariffs\Services\Yandex\Settings;
use Magnetar\Tariffs\Services\Yandex\YaMoneyCommonHttpProtocol;

class CallbackController extends Controller
{
    /**
     * Yandex payment callback.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function paymentAviso(Request $request)
    {
        DB::beginTransaction();
        try {
            $settings = new Settings();
            $yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("paymentAviso", $settings);
            $yaMoneyCommonHttpProtocol->processRequest($request->all());
            DB::commit();
            exit;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
            exit;
        }
    }

    /**
     * Yandex password callback.
     *
     * @param Request $request
     */
    public function checkOrder(Request $request)
    {
        $settings = new Settings();
        $yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("checkOrder", $settings);
        $yaMoneyCommonHttpProtocol->processRequest($request->all());
        exit;
    }
}
