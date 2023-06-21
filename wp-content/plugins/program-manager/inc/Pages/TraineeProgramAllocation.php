<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;
use WP_REST_Response;

class TraineeProgramAllocation
{
    public function register()
    {
        $this->create_trainee_program_allocation_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function create_trainee_program_allocation_table()
    {
        global $wpdb;
        // $programs_table = $wpdb->prefix . 'programs';
        // $trainers_table = $wpdb->prefix . 'trainers';
        $allocation_table = $wpdb->prefix . 'program_trainees_allocation';

        $sql = "CREATE TABLE IF NOT EXISTS $allocation_table (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    program_id INT NOT NULL,
                    trainee_id INT NOT NULL,
                    assigned_on DATE NOT NULL DEFAULT CURRENT_DATE
                );";
        // FOREIGN KEY (program_id) REFERENCES $programs_table (program_id),
        // FOREIGN KEY (trainer_id) REFERENCES $trainers_table (trainer_id)
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/trainees/allocate-program', [
            'methods' => 'POST',
            'callback' => [$this, 'register_in_program'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
    }

    public function register_in_program($request)
    {
        $program_id = $request['program_id'];
        $trainee_id = $request['trainee_id'];

        global $wpdb;
        $allocation_table = $wpdb->prefix . 'program_trainees_allocation';

        $res = $wpdb->insert($allocation_table, [
            'program_id' => $program_id,
            'trainee_id' => $trainee_id
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response("Error registering to program", 500);
        }

        return new WP_REST_Response("Trainee Added To Program", 201);
    }
}
