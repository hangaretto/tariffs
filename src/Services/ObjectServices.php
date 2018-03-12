<?php

namespace Magnetar\Tariffs\Services;

class ObjectServices
{
    const MAGNETAR_TARIFFS_TARIFFS = 'tariffs';
    const MAGNETAR_TARIFFS_PACKAGES = 'packages';

    const MAGNETAR_TARIFFS_TYPES = [
        self::MAGNETAR_TARIFFS_TARIFFS => 1,
        self::MAGNETAR_TARIFFS_PACKAGES => 2,
    ];

    /**
     * get type by code
     *
     * @param string $type
     * @return int
     * @throws
     */
    public static function getTypeId($type)
    {
        if(!isset(self::MAGNETAR_TARIFFS_TYPES[$type]))
            throw new \Exception('access.denied');

        return self::MAGNETAR_TARIFFS_TYPES[$type];
    }
}