<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_REST_Response;

class EmployeeRoutes
{
    public $fields = ['ID', 'user_email', 'user_registered', 'roles'];

    public function register()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/employees', [
            'methods' => "GET",
            'callback' => [$this, 'get_employees'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
        register_rest_route('api/v1', '/employees/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_employee'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/employees/search', [
            'methods' => "GET",
            'callback' => [$this, 'search_employees'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/employees/created_by/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_employees_created_by'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);

        register_rest_route('api/v1', '/employees', [
            'methods' => 'POST',
            'callback' => [$this, 'create_employee'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/login', [
            'methods' => 'POST',
            'callback' => [$this, 'login'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/employees/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_employee'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/employees/deactivate/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'deactivate_employee'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/employees/activate/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'activate_employee'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/employees/delete/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'delete_employee'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
        register_rest_route('api/v1', '/employees/restore/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'restore_employee'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
    }

    function format_user_data($user)
    {
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
            'created_by' => get_user_meta($user->ID, 'created_by', true) ? get_user_meta($user->ID, 'created_by', true) : '0',
        ];
    }

    public function get_response_object($code, $message, $data = null)
    {
        $res = ["code" => $code];

        if (isset($message)) {
            $res['message'] = $message;
        }

        if ($data !== null) {
            $res['data'] = $data;
        }
        return $res;
    }

    public function get_employees($request)
    {
        $role = $request->get_param('role');
        $roles = [];

        if ($role) {
            $roles = [$role];
        } else {
            $roles = ['program_manager', 'trainer', 'trainee'];
        }

        $users = get_users([
            'role__in' => $roles,
            'fields' => $this->fields
        ]);
        $res = array_map([$this, 'format_user_data'], $users);

        return new WP_REST_Response($this->get_response_object(200, null, $res));
    }

    public function get_single_employee($request)
    {
        $id = $request->get_param('id');

        $users = get_users([
            'include' => [$id],
            'role__in' => ['program_manager', 'trainer', 'trainee'],
            'fields' => $this->fields
        ]);

        $res = array_map([$this, 'format_user_data'], $users);

        $res = reset($res);
        if (!$res) {
            return new WP_REST_Response($this->get_response_object(404, "User Not Found"), 404);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $res));
    }


    public function search_employees($request)
    {
        $res = [];

        $users = get_users([
            'role__in' => ['administrator', 'program_manager', 'trainer', 'trainee'],
            'fields' => $this->fields
        ]);

        $res = array_map([$this, 'format_user_data'], $users);

        $q = $request->get_param('q');


        $filtered_users = array_filter($res, function ($user) use ($q) {
            $fullname_lower = strtolower($user['fullname']);
            $email_lower = strtolower($user['email']);
            return (strpos($fullname_lower, $q) !== false || strpos($email_lower, $q) !== false) && $user['is_deactivated'] == '0' && $user['is_deleted'] == '0';
        });

        return new WP_REST_Response($this->get_response_object(200, null, array_values($filtered_users)));
    }


    public function get_employees_created_by($request)
    {
        $created_by = $request->get_param('id');
        $role = $request->get_param('role');
        $roles = [];

        if ($role) {
            $roles = [$role];
        } else {
            $roles = ['program_manager', 'trainer', 'trainee'];
        }

        $users = get_users([
            'role__in' => $roles,
            'fields' => $this->fields
        ]);
        $res = array_map([$this, 'format_user_data'], $users);

        $filtered_users = array_filter($res, function ($user) use ($created_by) {
            return $user['created_by'] == $created_by;
        });

        return new WP_REST_Response($this->get_response_object(200, null, array_values($filtered_users)));
    }

    public function create_employee($request)
    {
        $email = $request['email'];
        $password = $request['password'];
        $fullname = $request['fullname'];
        $role = $request['role'];
        $created_by = $request['created_by'];

        $missingParams = array();

        if (!isset($email)) {
            $missingParams[] = "email";
        }
        if (!isset($password)) {
            $missingParams[] = "password";
        }
        if (!isset($fullname)) {
            $missingParams[] = "fullname";
        }
        if (!isset($role)) {
            $missingParams[] = "role";
        }
        if (!isset($created_by)) {
            $missingParams[] = "created_by";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        $result = wp_insert_user([
            'user_login' => $email,
            'user_email' => $email,
            'user_pass' => $password,
            'role' => $role,
            'meta_input' => [
                'is_deactivated' => $request['is_deactivated'] ?? 0,
                'is_deleted' => 0,
                'fullname' => $fullname,
                'created_by' => $created_by
            ]
        ]);

        if (is_wp_error($result)) {
            return new WP_REST_Response($this->get_response_object(500, 'Employee Creation Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(201, "User Created", $result), 201);
    }

    public function login($request)
    {
        $user = wp_signon([
            'user_login' => $request['email'],
            'user_password' => $request['password']
        ]);

        if (is_wp_error($user)) {
            return new WP_REST_Response($user->get_error_message(), 500);
        }
        return new WP_REST_Response($user);
    }

    public function update_employee($request)
    {
        $user_id = $request->get_param('id');

        $password = $request['password'];
        $fullname = $request['fullname'];

        $missingParams = array();

        if (!isset($password)) {
            $missingParams[] = "password";
        }
        if (!isset($fullname)) {
            $missingParams[] = "fullname";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        $result = wp_update_user([
            'ID' => $user_id,
            'user_pass' => $password,
            'meta_input' => [
                'fullname' => $fullname,
            ]
        ]);

        if (is_wp_error($result)) {
            return new WP_REST_Response($this->get_response_object(500, 'Employee Update Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "User Updated Successfully", $user_id));
    }

    public function deactivate_employee($request)
    {
        $id = $request->get_param('id');
        if ($id == 1) {
            return new WP_REST_Response($this->get_response_object(500, 'Admin Cannot Be Deactivated'), 500);
        }
        $meta_id = update_user_meta($id, "is_deactivated", 1);

        if (!$meta_id) {
            return new WP_REST_Response($this->get_response_object(500, 'Deactivation Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "User Deactivated Successfully"));
    }


    public function activate_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id = update_user_meta($id, "is_deactivated", 0);

        if (!$meta_id) {
            return new WP_REST_Response($this->get_response_object(500, 'Activation Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "User Activated"));
    }

    public function delete_employee($request)
    {
        $id = $request->get_param('id');
        if ($id == 1) {
            return new WP_REST_Response($this->get_response_object(500, 'Admin Cannot Be Deleted'), 500);
        }
        $meta_id_deactivate = update_user_meta($id, "is_deactivated", 1);
        $meta_id = update_user_meta($id, "is_deleted", 1);

        if (!$meta_id) {
            return new WP_REST_Response($this->get_response_object(500, 'Deletion Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "User Deleted"));
    }


    public function restore_employee($request)
    {
        $id = $request->get_param('id');
        $meta_id = update_user_meta($id, "is_deleted", 0);

        if (!$meta_id) {
            return new WP_REST_Response($this->get_response_object(500, 'Restoration Failed'), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "User Restored"));
    }
}
