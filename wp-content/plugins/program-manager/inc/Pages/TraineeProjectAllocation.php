<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;
use WP_REST_Response;

class TraineeProjectAllocation
{
    public function register()
    {
        $this->create_trainee_project_allocation_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function create_trainee_project_allocation_table()
    {
        global $wpdb;
        // $projects_table = $wpdb->prefix . 'projects';
        // $trainers_table = $wpdb->prefix . 'trainers';
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';

        $sql = "CREATE TABLE IF NOT EXISTS $allocation_table (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    project_id INT NOT NULL,
                    trainee_id INT NOT NULL,
                    assigned_on DATE NOT NULL DEFAULT CURRENT_DATE
                );";
        // FOREIGN KEY (project_id) REFERENCES $projects_table (project_id),
        // FOREIGN KEY (trainer_id) REFERENCES $trainers_table (trainer_id)
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/projects/allocate', [
            'methods' => 'POST',
            'callback' => [$this, 'allocate_trainee'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
    }

    public function get_response_object($code, $message, $data = NULL)
    {
        $res = ["code" => $code];

        if ($message) {
            $res['message'] = $message;
            return $res;
        }

        if ($data) {
            $res['data'] = $data;
            return $res;
        }
        return $res;
    }

    public function allocate_trainee($request)
    {
        $project_id = $request['project_id'];
        $trainee_id = $request['trainee_id'];

        global $wpdb;
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';

        $res = $wpdb->insert($allocation_table, [
            'project_id' => $project_id,
            'trainee_id' => $trainee_id
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(400, "Error registering to project"), 400);
        }

        return new WP_REST_Response($this->get_response_object(201, "Trainee Allocated Project"), 201);
    }
}
