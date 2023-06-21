<?php

/**
 * @package ProgramManager
 */

namespace Inc;

use Inc\Pages\EmployeeRoutes;
use Inc\Pages\RoleManager;
use Inc\Pages\ProgramRoutes;
use Inc\Pages\ProjectRoutes;
use Inc\Pages\TaskRoutes;
use Inc\Pages\TraineeProgramAllocation;
use Inc\Pages\TraineeProjectAllocation;

class Init
{
    public static function get_services()
    {
        return [
            RoleManager::class,
            EmployeeRoutes::class,
            ProjectRoutes::class,
            ProgramRoutes::class,
            TraineeProgramAllocation::class,
            TraineeProjectAllocation::class,
            TaskRoutes::class
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
