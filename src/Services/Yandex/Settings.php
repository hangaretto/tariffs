<?php

namespace Magnetar\Tariffs\Services\Yandex;

class Settings {
    public $SHOP_PASSWORD = "pipercat2020";
    public $SECURITY_TYPE;
    public $LOG_FILE;
    public $SHOP_ID = 160091;
    public $CURRENCY = 10643;
    public $request_source;
    public $mws_cert;
    public $mws_private_key;
    public $mws_cert_password = "123456";
    function __construct($SECURITY_TYPE = "MD5" /* MD5 | PKCS7 */, $request_source = "php://input") {
        $this->SECURITY_TYPE = $SECURITY_TYPE;
        $this->request_source = $request_source;
        $this->LOG_FILE = "log.txt";
        $this->mws_cert = "mws/shop.cer";
        $this->mws_private_key = "mws/private.key";
    }
}