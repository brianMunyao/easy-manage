<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;
use WP_REST_Response;

class ProgramRoutes
{
    public function register()
    {
        $this->create_programs_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    public function create_programs_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            program_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            program_name TEXT NOT NULL,
            program_description TEXT NOT NULL,
            program_logo TEXT NOT NULL,
            program_assigned_to BIGINT(20) UNSIGNED,
            program_created_by INT NOT NULL,
            program_created_on DATE NOT NULL DEFAULT CURRENT_DATE,
            program_done INT NOT NULL DEFAULT 0,
            program_start_date DATE NOT NULL DEFAULT CURRENT_DATE,
            program_end_date DATE NOT NULL DEFAULT DATE_ADD(CURRENT_DATE(), INTERVAL 3 MONTH)
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/programs/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_programs'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/programs/single/(?P<pg_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_program'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/programs/trainees/(?P<pg_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_program_trainees'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/programs/assigned_to/(?P<trainer_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_trainer_program'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/programs', [
            'methods' => 'POST',
            'callback' => [$this, 'create_program'],
            'permission_callback' => function () {
                return current_user_can('publish_posts');
            }
        ]);
        register_rest_route('api/v1', '/programs/unassigned/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_unassigned_programs'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/programs/allocate', [
            'methods' => "PUT",
            'callback' => [$this, 'allocate_program'],
            'permission_callback' => function () {
                return current_user_can('publish_posts');
            }
        ]);
        register_rest_route('api/v1', '/programs/(?P<id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'update_program'],
            'permission_callback' => function () {
                return current_user_can('publish_posts');
            }
        ]);
        register_rest_route('api/v1', '/programs/(?P<program_id>\d+)', [
            'methods' => "DELETE",
            'callback' => [$this, 'delete_program'],
            'permission_callback' => function () {
                return current_user_can('publish_posts');
            }
        ]);
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

    public function get_programs($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $id = $request->get_param('id');

        $programs = $wpdb->get_results("SELECT * FROM $table_name WHERE program_created_by=$id");

        return new WP_REST_Response($this->get_response_object(200, null, $programs));
    }

    public function get_single_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $pg_id = $request->get_param('pg_id');

        $program = $wpdb->get_row("SELECT * FROM $table_name WHERE program_id=$pg_id");

        if (!$program) {
            return new WP_REST_Response($this->get_response_object(404, 'Program Not Found'), 404);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $program));
    }

    public function get_trainer_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $trainer_id = $request->get_param('trainer_id');

        $program = $wpdb->get_row("SELECT * FROM $table_name WHERE program_assigned_to=$trainer_id");

        if (!$program) {
            return new WP_REST_Response($this->get_response_object(204, "Trainer Not Assigned A Program"), 204);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $program));
    }

    public function get_unassigned_programs($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $id = $request->get_param('id');

        $programs = $wpdb->get_results("SELECT * FROM $table_name WHERE program_created_by=$id AND program_assigned_to IS NULL");

        return new WP_REST_Response($this->get_response_object(200, null, $programs));
    }

    public function get_program_trainees($request)
    {
        $pg_id = $request->get_param('pg_id');

        global $wpdb;
        // $table_name = $wpdb->prefix . 'programs';
        $allocation_table = $wpdb->prefix . 'program_trainees_allocation';
        $program_trainees = $wpdb->get_results("SELECT * FROM $allocation_table WHERE program_id=$pg_id");

        $ids = array_map(function ($item) {
            return $item->trainee_id;
        }, $program_trainees);

        if (count($ids) > 0) {
            $users = get_users([
                'include' => $ids,
                'fields' => ['ID', 'user_email', 'user_registered', 'roles']
            ]);

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
                    'created_by' => get_user_meta($user->ID, 'created_by', true) ? get_user_meta($user->ID, 'created_by', true) : '0',
                ];
            }, $users);
            return new WP_REST_Response($this->get_response_object(200, null, $res));
        }
        return new WP_REST_Response($this->get_response_object(200, null, []));
    }


    public function create_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';

        $program_name = $request['program_name'];
        $program_description = $request['program_description'];
        $program_logo = $request['program_logo'];
        $program_created_by = $request['program_created_by'];

        $missingParams = array();

        if (!isset($program_name)) {
            $missingParams[] = "program_name";
        }
        if (!isset($program_description)) {
            $missingParams[] = "program_description";
        }
        if (!isset($program_logo)) {
            $missingParams[] = "program_logo";
        }
        if (!isset($program_created_by)) {
            $missingParams[] = "program_created_by";
        }
        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }


        $res = $wpdb->insert($table_name, [
            'program_name' => $program_name,
            'program_description' => $program_description,
            'program_logo' => $program_logo,
            // 'program_assigned_to'=>$request['program_assigned_to'],
            'program_created_by' => $program_created_by
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error creating program"), 500);
        }
        return new WP_REST_Response($this->get_response_object(201, "Program Added Successfully", $wpdb->insert_id), 201);
    }

    public function allocate_program($request)
    {
        $program_id = $request['program_id'];
        $trainer_id = $request['trainer_id'];

        $missingParams = array();

        if (!isset($program_id)) {
            $missingParams[] = "program_id";
        }
        if (!isset($trainer_id)) {
            $missingParams[] = "trainer_id";
        }
        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }


        global $wpdb;
        $programs_table = $wpdb->prefix . 'programs';

        $current_allocations = $wpdb->get_results("SELECT program_assigned_to FROM $programs_table WHERE program_assigned_to=$trainer_id");

        if (count($current_allocations) > 0) {
            return new WP_REST_Response($this->get_response_object(409, "Trainer already has an ongoing program"), 409);
        }

        $res = $wpdb->update($programs_table, [
            'program_assigned_to' => $trainer_id
        ], ['program_id' => $program_id]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error allocating program"), 500);
        } elseif (!$res) {
            return new WP_REST_Response($this->get_response_object(400, "Invalid Trainer ID or Program ID"), 400);
        }
        return new WP_REST_Response($this->get_response_object(200, "Trainer Allocated Successfully"));
    }

    public function update_program($request)
    {
        $id = $request->get_param('id');

        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';

        $program_name = $request['program_name'];
        $program_description = $request['program_description'];
        $program_logo = $request['program_logo'];

        $missingParams = array();

        if (!isset($program_name)) {
            $missingParams[] = "program_name";
        }
        if (!isset($program_description)) {
            $missingParams[] = "program_description";
        }
        if (!isset($program_logo)) {
            $missingParams[] = "program_logo";
        }
        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        $res = $wpdb->update($table_name, [
            'program_name' => $program_name,
            'program_description' => $program_description,
            'program_logo' => $program_logo,
        ], ['program_id' => $id]);

        if ($res > 0) {
            return new WP_REST_Response($this->get_response_object(200, "Program Updated Successfully"));
        }
        return new WP_REST_Response($this->get_response_object(500, "Error updating program"), 500);
    }

    public function delete_program($request)
    {
        $program_id = $request->get_param('program_id');

        global $wpdb;
        $programs_table = $wpdb->prefix . 'programs';

        $res = $wpdb->delete($programs_table, ['program_id' => $program_id]);

        return new WP_REST_Response($this->get_response_object(204, "Program Deleted", $program_id), 204);
    }
}
