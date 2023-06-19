<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;

class ManageProgram
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
            program_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            program_name TEXT NOT NULL,
            program_description TEXT NOT NULL,
            program_logo TEXT NOT NULL,
            program_assigned_to TEXT,
            program_created_by INT NOT NULL,
            program_created_on DATE NOT NULL DEFAULT CURRENT_DATE,
            program_done INT NOT NULL DEFAULT 0
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/programs/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_programs'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/single/(?P<pg_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_program'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/trainees/(?P<pg_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_program_trainees'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/assigned_to/(?P<trainer_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_trainer_program'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs', [
            'methods' => 'POST',
            'callback' => [$this, 'create_program'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/unassigned/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_unassigned_programs'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/allocate', [
            'methods' => "PUT",
            'callback' => [$this, 'allocate_program'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
        register_rest_route('api/v1', '/programs/(?P<id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'update_program'],
            // 'permission_callback' => function () {
            //     return current_user_can('manage_options');
            // }
        ]);
    }


    public function get_programs($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $id = $request->get_param('id');

        $programs = $wpdb->get_results("SELECT * FROM $table_name WHERE program_created_by=$id");

        return $programs;
    }

    public function get_single_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $pg_id = $request->get_param('pg_id');

        $program = $wpdb->get_row("SELECT * FROM $table_name WHERE program_id=$pg_id");

        return $program;
    }

    public function get_trainer_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $trainer_id = $request->get_param('trainer_id');

        $program = $wpdb->get_row("SELECT * FROM $table_name WHERE program_assigned_to=$trainer_id");

        // $program = ;
        if (!$program) {
            return new WP_Error(404, "Trainer Not Assigned A Program");
        }

        return $program;
    }

    public function get_unassigned_programs($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        // $allocation_table = $wpdb->prefix . 'program_trainers_allocation';
        $id = $request->get_param('id');

        $programs = $wpdb->get_results("SELECT * FROM $table_name WHERE program_created_by=$id AND program_assigned_to IS NULL");

        return $programs;
    }

    public function get_program_trainees($request)
    {
        $pg_id = $request->get_param('pg_id');

        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';
        $allocation_table = $wpdb->prefix . 'program_trainees_allocation';
        $program_trainees = $wpdb->get_results("SELECT * FROM $allocation_table WHERE program_id=$pg_id");

        $ids = array_map(function ($item) {
            return $item->trainee_id;
        }, $program_trainees);

        $users = get_users([
            'include' => $ids,
            'role__in' => ['trainee'],
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


        return $res;
    }


    public function create_program($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';

        $res = $wpdb->insert($table_name, [
            'program_name' => $request['program_name'],
            'program_description' => $request['program_description'],
            'program_logo' => $request['program_logo'],
            // 'program_assigned_to'=>$request['program_assigned_to'],
            'program_created_by' => $request['program_created_by']
        ]);

        if ($res > 0) {
            return "Program Added Successfully";
        }
        return new WP_Error(400, "Error creating program", $res);
    }

    public function allocate_program($request)
    {
        $program_id = $request['program_id'];
        $trainer_id = $request['trainer_id'];

        global $wpdb;
        $programs_table = $wpdb->prefix . 'programs';

        //TODO: Update this incase a program is completed
        $res = $wpdb->update($programs_table, [
            'program_assigned_to' => $trainer_id
        ], ['program_id' => $program_id]);
        // $current_allocations = $wpdb->get_results("SELECT * FROM $TPA_table JOIN $programs_table WHERE trainer_id=$trainer_id");

        // if (count($current_allocations) > 0) {
        //     return new WP_Error(400, "Trainer already has an ongoing program");
        // }
        // $res = $wpdb->insert($TPA_table, [
        //     'program_id' => $program_id,
        //     'trainer_id' => $trainer_id
        // ]);

        if (is_wp_error($res)) {
            return new WP_Error(400, "Error allocating program", $res);
        }
        return $res;
    }

    public function update_program($request)
    {
        $id = $request->get_param('id');

        global $wpdb;
        $table_name = $wpdb->prefix . 'programs';

        $res = $wpdb->update($table_name, [
            'program_name' => $request['program_name'],
            'program_description' => $request['program_description'],
            'program_logo' => $request['program_logo'],
        ], ['program_id' => $id]);

        if ($res > 0) {
            return "Program Updated Successfully";
        }
        return new WP_Error(400, "Error updating program", $res);
    }
}
