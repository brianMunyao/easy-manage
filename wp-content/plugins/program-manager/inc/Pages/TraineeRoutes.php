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
        register_rest_route('api/v1', '/trainees/(?P<program_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trainees_in_program'],
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
                'created_by' => get_user_meta($pm->ID, 'created_by', true) ? get_user_meta($pm->ID, 'created_by', true) : 1,
            ];
        }, $pms);

        $pm_id = $request->get_param('pm_id');
        if (isset($pm_id)) {
            $res = array_filter($res, function ($user) use ($pm_id) {
                return (string)$user['created_by'] == (string)$pm_id;
            });
        }

        return $res;

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

    public function get_trainees_in_program($request)
    {
        global $wpdb;

        $users_table = $wpdb->get_prefix . 'users';
        $program_trainees_allocation_table = $wpdb->get_prefix . 'program_trainees_allocation';
        $program_id = $request->get_param('program_id');

        $results = $wpdb->get_results("SELECT * FROM $users_table JOIN $program_trainees_allocation_table ON program_id=$program_id");

        return $results;
    }
}
