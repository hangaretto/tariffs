<?php

namespace Magnetar\Tariffs\Services\Yandex;

class Settings {
    public $SHOP_PASSWORD;
    public $SECURITY_TYPE;
    public $LOG_FILE;
    public $SHOP_ID;
    public $CURRENCY;
    public $request_source;
    public $mws_cert;
    public $mws_private_key;
    public $mws_cert_password;
    function __construct($SECURITY_TYPE = "MD5" /* MD5 | PKCS7 */, $request_source = "php://input") {
        $this->SHOP_ID = config('magnetar.tariffs.services.yandex.shopId');
        $this->SHOP_PASSWORD = config('magnetar.tariffs.services.yandex.shopPassword');
        $this->mws_cert_password = config('magnetar.tariffs.services.yandex.certPassword');
        $this->CURRENCY = config('magnetar.tariffs.services.yandex.currency');
        $this->SECURITY_TYPE = $SECURITY_TYPE;
        $this->request_source = $request_source;
        $this->LOG_FILE = "log.txt";
        $this->mws_cert = public_path("mws/shop.cer");
        $this->mws_private_key = public_path("mws/private.key");
    }
}