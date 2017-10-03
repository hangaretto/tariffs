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
    public function paymentAviso(Request $request) {

        $arr = [
            'cps_shopPaymentType' => 'AC',
            'orderSumAmount' => '100.00',
            'cdd_exp_date' => '1019',
            'shopArticleId' => '473063',
            'paymentPayerCode' => '4100322062290',
            'cdd_rrn' => '729678550126',
            'external_id' => 'deposit',
            'paymentType' => 'AC',
            'requestDatetime' => '2017-10-02T08:30:31.931+03:00',
            'depositNumber' => 'L5vJTeObmwskFC0yCEqn4HjmRrMZ.001f.201710',
            'cdd_response_code' => '00',
            'cps_user_country_code' => 'PL',
            'orderCreatedDatetime' => '2017-10-02T08:30:31.868+03:00',
            'sk' => 'yeb505d4c3b5d151a94bdfb4c694ee51d',
            'action' => 'checkOrder',
            'shopId' => '160091',
            'scid' => '558710',
            'shopSumBankPaycash' => '1003',
            'shopSumCurrencyPaycash' => '10643',
            'rebillingOn' => false,
            'orderSumBankPaycash' => '1003',
            'cps_region_id' => '65',
            'orderSumCurrencyPaycash' => '10643',
            'merchant_order_id' => '100601_021017083007_00000_160091',
            'unilabel' => '2163e29f-0009-5000-8000-000026b64227',
            'module' => 'magnetar_billing',
            'cdd_pan_mask' => '444444|4448',
            'customerNumber' => '100601',
            'yandexPaymentId' => '25700121309101',
            'environment' => 'Live',
            'invoiceId' => '2000001626659',
            'shopSumAmount' => '96.50',
            'md5' => 'C04EFFCB3651D9F786AB262BF1C68EAE',
        ];
//dd($arr);
        $settings = new Settings();
        $yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("paymentAviso", $settings);
        $yaMoneyCommonHttpProtocol->processRequest($request->all());
        exit;

    }

    /**
     * Yandex password callback.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function checkOrder(Request $request) {

        $settings = new Settings();
        $yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("checkOrder", $settings);
        $yaMoneyCommonHttpProtocol->processRequest($request->all());
        exit;

    }

}
