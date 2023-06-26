<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use Inc\Base\BaseController;
use WP_REST_Response;

class TraineeProgramAllocation extends BaseController
{
    public function register()
    {
        $this->create_trainee_program_allocation_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function create_trainee_program_allocation_table()
    {
        global $wpdb;
        $programs_table = $wpdb->prefix . 'programs';
        $users_table = $wpdb->prefix . 'users';
        $allocation_table = $wpdb->prefix . 'program_trainees_allocation';

        $sql = "CREATE TABLE IF NOT EXISTS $allocation_table (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    program_id INT NOT NULL,
                    trainee_id BIGINT(20) UNSIGNED NOT NULL,
                    assigned_on DATE NOT NULL DEFAULT CURRENT_DATE,
                    CONSTRAINT FK_trainee_id FOREIGN KEY (trainee_id) REFERENCES $users_table(ID),
                    CONSTRAINT FK_program_id FOREIGN KEY (program_id) REFERENCES $programs_table(program_id)
                );";
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
            return new WP_REST_Response($this->get_response_object(500, "Error registering to program. " . $res->get_message()), 500);
        }

        return new WP_REST_Response($this->get_response_object(201, "Trainee Added To Program"), 201);
    }
}
