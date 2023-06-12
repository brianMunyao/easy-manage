<?php

/**
 * @package ProgramManager
 */

namespace Inc;

use Inc\Pages\EmployeeRoutes;
use Inc\Pages\PMRoutes;
use Inc\Pages\RoleManager;
use Inc\Pages\TraineeRoutes;
use Inc\Pages\TrainerRoutes;
use Inc\Pages\CreateTables;
use Inc\Pages\ProjectRoutes;

class Init
{
    public static function get_services()
    {
        return [
            RoleManager::class,
            PMRoutes::class,
            TrainerRoutes::class,
            TraineeRoutes::class,
            EmployeeRoutes::class,
            CreateTables::class,
            ProjectRoutes::class
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
