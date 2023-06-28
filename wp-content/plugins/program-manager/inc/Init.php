<?php

/**
 * @package ProgramManager
 */

namespace Inc;

use Inc\Routes\EmployeeRoutes;
use Inc\Utils\RoleManager;
use Inc\Routes\ProgramRoutes;
use Inc\Routes\ProjectRoutes;
use Inc\Routes\TaskRoutes;
use Inc\Routes\TraineeProgramAllocation;
use Inc\Routes\TraineeProjectAllocation;

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
