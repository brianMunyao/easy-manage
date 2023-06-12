<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;

class TrainerRoutes
{
    public function register()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/trainers', [
            'methods' => "GET",
            'callback' => [$this, 'get_trainer'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/trainers', [
            'methods' => 'POST',
            'callback' => [$this, 'create_trainer'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
    }

    public function get_trainer($request)
    {
        $res = [];

        $pms = get_users(['role' => 'trainer']);

        $res = array_map(function ($pm) {
            return [
                'id' => $pm->ID,
                'fullname' => get_user_meta($pm->ID, 'fullname', true) ? get_user_meta($pm->ID, 'fullname', true) : $pm->user_login,
                'email' => $pm->user_email,
                'registered_on' => $pm->user_registered,
                'role' => 'trainer',
                'is_deactivated' => get_user_meta($pm->ID, 'is_deactivated', true) ? get_user_meta($pm->ID, 'is_deactivated', true) : '0',
                'is_deleted' => get_user_meta($pm->ID, 'is_deleted', true) ? get_user_meta($pm->ID, 'is_deleted', true) : '0',
            ];
        }, $pms);

        return $res;
    }

    public function create_trainer($request)
    {
        $result = wp_insert_user([
            'user_login' => $request['email'],
            'user_email' => $request['email'],
            'user_pass' => $request['password'],
            'role' => 'trainer',
            'meta_input' => [
                'is_deactivated' => 0,
                'is_deleted' => 0,
                'fullname' => $request['fullname']
            ]
        ]);

        if (is_wp_error($result)) {
            return new WP_Error(400, 'Trainer Creation Failed', $result);
        }
        return $result;
    }
}