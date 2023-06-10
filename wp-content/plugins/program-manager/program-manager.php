<?php

/**
 * @package ProgramManager
 */


/**
 * Plugin Name: Program Manager
 * Plugin URI:  https://example.com
 * Description: Go to plugin for training programs and similar programs
 * Version:     1.0.0
 * Author:      Brian
 * Author URI:  https://example.com
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
defined('ABSPATH') or die("Blocked");


use Inc\Init;
use Inc\Base\Activate;
use Inc\Base\Deactivate;


if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once(dirname(__FILE__) . '/vendor/autoload.php');
}

function activate_pm_plugin()
{
    Activate::activate();
}
register_activation_hook(__FILE__, 'activate_pm_plugin');

function deactivate_pm_plugin()
{
    Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_pm_plugin');

if (class_exists('Inc\\Init')) {
    Init::register_services();
}
