<?php

namespace Magnetar\Tariffs\Services\Yandex;

class Log {

    private $settings;

    function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function info($str)
    {
        if(is_array($str) || is_object($str))
            $str = print_r($str,true);
        $str = $str . "\n";
        \Log::debug('-----tariff log-----');
        \Log::debug($str);
    }
}