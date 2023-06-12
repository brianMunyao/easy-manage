<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;

class TraineeRoutes
{
    public function register()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/trainees', [
            'methods' => "GET",
            'callback' => [$this, 'get_trainee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/trainees', [
            'methods' => 'POST',
            'callback' => [$this, 'create_trainee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
    }

    public function get_trainee($request)
    {
        $res = [];

        $pms = get_users(['role' => 'trainee']);

        $res = array_map(function ($pm) {
            return [
                'id' => $pm->ID,
                'fullname' => get_user_meta($pm->ID, 'fullname', true) ? get_user_meta($pm->ID, 'fullname', true) : $pm->user_login,
                'email' => $pm->user_email,
                'registered_on' => $pm->user_registered,
                'role' => 'trainee',
                'is_deactivated' => get_user_meta($pm->ID, 'is_deactivated', true) ? get_user_meta($pm->ID, 'is_deactivated', true) : '0',
                'is_deleted' => get_user_meta($pm->ID, 'is_deleted', true) ? get_user_meta($pm->ID, 'is_deleted', true) : '0',
            ];
        }, $pms);

        return $res;
    }

    public function create_trainee($request)
    {
        $result = wp_insert_user([
            'user_login' => $request['email'],
            'user_email' => $request['email'],
            'user_pass' => $request['password'],
            'role' => 'trainee',
            'meta_input' => [
                'is_deactivated' => 0,
                'is_deleted' => 0,
                'fullname' => $request['fullname']
            ]
        ]);

        if (is_wp_error($result)) {
            return new WP_Error(400, 'Trainee Creation Failed', $result);
        }
        return $result;
    }
}
