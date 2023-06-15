<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;

class EmployeeRoutes
{
    public function register()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/employees', [
            'methods' => "GET",
            'callback' => [$this, 'get_employees'],
            // 'permission_callback' => function () {
            //     return current_user_can('read');
            // }
        ]);
        register_rest_route('api/v1', '/employees/search', [
            'methods' => "GET",
            'callback' => [$this, 'search_employees'],
            // 'permission_callback' => function () {
            //     return current_user_can('read');
            // }
        ]);

        register_rest_route('api/v1', '/employees', [
            'methods' => 'POST',
            'callback' => [$this, 'create_employee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/employees/deactivate/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'deactivate_employee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/employees/activate/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'activate_employee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/employees/delete/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'delete_employee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/employees/restore/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'restore_employee'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
    }

    public function get_employees($request)
    {
        $users = get_users(['role__in' => ['program_manager', 'trainer', 'trainee'], 'fields' => ['ID', 'user_email', 'user_registered', 'roles']]);
        $res = array_map(function ($user) {
            $roles = get_user_meta($user->ID, 'wp_capabilities', true);
            $role = array_keys($roles)[0];
            return [
                'id' => $user->ID,
                'fullname' => get_user_meta($user->ID, 'fullname', true) ? get_user_meta($user->ID, 'fullname', true) : $user->user_email,
                'email' => $user->user_email,
                'registered_on' => $user->user_registered,
                'role' => $role,
                'is_deactivated' => get_user_meta($user->ID, 'is_deactivated', true) ? get_user_meta($user->ID, 'is_deactivated', true) : '0',
                'is_deleted' => get_user_meta($user->ID, 'is_deleted', true) ? get_user_meta($user->ID, 'is_deleted', true) : '0',
            ];
        }, $users);

        return $res;
    }

    public function search_employees($request)
    {
        $res = [];

        $users = get_users(['role__in' => ['administrator', 'program_manager', 'trainer', 'trainee'], 'fields' => ['ID', 'user_email', 'user_registered', 'roles']]);

        $res = array_map(function ($user) {
            $roles = get_user_meta($user->ID, 'wp_capabilities', true);
            $role = array_keys($roles)[0];
            return [
                'id' => $user->ID,
                'fullname' => get_user_meta($user->ID, 'fullname', true) ? get_user_meta($user->ID, 'fullname', true) : $user->user_email,
                'email' => $user->user_email,
                'registered_on' => $user->user_registered,
                'role' => $role,
                'is_deactivated' => get_user_meta($user->ID, 'is_deactivated', true) ? get_user_meta($user->ID, 'is_deactivated', true) : '0',
                'is_deleted' => get_user_meta($user->ID, 'is_deleted', true) ? get_user_meta($user->ID, 'is_deleted', true) : '0',
            ];
        }, $users);

        $q = $request->get_param('q');


        $filtered_users = array_filter($res, function ($user) use ($q) {
            $fullname_lower = strtolower($user['fullname']);
            $email_lower = strtolower($user['email']);
            return (strpos($fullname_lower, $q) !== false || strpos($email_lower, $q) !== false) && $user['is_deactivated'] == '0' && $user['is_deleted'] == '0';
        });

        // return count($filtered_users);
        return array_values($filtered_users);
    }

    public function create_employee($request)
    {
        $result = wp_insert_user([
            'user_login' => $request['email'],
            'user_email' => $request['email'],
            'user_pass' => $request['password'],
            'role' => $request['role'],
            'meta_input' => [
                'is_deactivated' => 0,
                'is_deleted' => 0,
                'fullname' => $request['fullname'],
                'created_by' => $request['created_by']
            ]
        ]);

        if (is_wp_error($result)) {
            return new WP_Error(400, 'Program Manager Creation Failed', $result);
        }
        return $result;
    }

    public function deactivate_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id = update_user_meta($id, "is_deactivated", 1);

        if (!$meta_id) {
            return new WP_Error(400, 'Deactivation Failed ', $meta_id);
        }
        return $meta_id;
    }


    public function activate_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id = update_user_meta($id, "is_deactivated", 0);

        if (!$meta_id) {
            return new WP_Error(400, 'Activation Failed ', $meta_id);
        }
        return $meta_id;
    }

    public function delete_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id_deactivate = update_user_meta($id, "is_deactivated", 1);
        $meta_id = update_user_meta($id, "is_deleted", 1);

        if (!$meta_id) {
            return new WP_Error(400, 'User Deleted Failed', $meta_id);
        }
        return $meta_id;
    }


    public function restore_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id = update_user_meta($id, "is_deleted", 0);

        if (!$meta_id) {
            return new WP_Error(400, 'Restored Failed ', $meta_id);
        }
        return $meta_id;
    }
}
