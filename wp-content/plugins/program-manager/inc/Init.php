<?php

/**
 * @package ProgramManager
 */

namespace Inc;

use Inc\Pages\RoleManager;

class Init
{
    public static function get_services()
    {
        return [
            RoleManager::class
        ];
    }


    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service_instance = new $class;

            if (method_exists($service_instance, 'register')) {
                $service_instance->register();
            }
        }
    }
}
