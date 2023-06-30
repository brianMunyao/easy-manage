<?php


/**
 * @package ProgramManager
 */

namespace Inc\Routes;

use Inc\Base\BaseController;
use WP_REST_Response;

class RemarkRoutes extends BaseController
{
    public function register()
    {
        $this->create_remarks_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/remarks', [
            'methods' => "GET",
            'callback' => [$this, 'get_all_remarks'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/remarks/(?P<project_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_project_remark'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/remarks/(?P<project_id>\d+)', [
            'methods' => "POST",
            'callback' => [$this, 'add_project_remark'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
    }

    public function create_remarks_table()
    {
        global $wpdb;
        $remarks_table = $wpdb->prefix . 'remarks';
        $projects_table = $wpdb->prefix . 'projects';
        $users_table = $wpdb->prefix . 'users';

        $sql = "CREATE TABLE IF NOT EXISTS $remarks_table (
            remark_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            remark_desc TEXT NOT NULL,
            remark_marks INT,
            remark_created_by BIGINT(20) UNSIGNED NOT NULL,
            remark_project_id INT NOT NULL,
            CONSTRAINT FK_remark_created_by FOREIGN KEY (remark_created_by) REFERENCES $users_table(ID),
            CONSTRAINT FK_remark_project_id FOREIGN KEY (remark_project_id) REFERENCES $projects_table(project_id)
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function get_all_remarks($request)
    {
        global $wpdb;
        $remarks_table = $wpdb->prefix . 'remarks';

        $remarks = $wpdb->get_results("SELECT * FROM $remarks_table");
        return new WP_REST_Response($this->get_response_object(200, null, $remarks));
    }

    public function get_project_remark($request)
    {
        $project_id = $request->get_param('project_id');
        global $wpdb;
        $remarks_table = $wpdb->prefix . 'remarks';

        $remark = $wpdb->get_row("SELECT * FROM $remarks_table WHERE remark_project_id=$project_id");
        if (!$remark) {
            return new WP_REST_Response($this->get_response_object(404, 'Remark does not exist'), 404);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $remark));
    }

    public function add_project_remark($request)
    {
        $remark_desc = $request['remark_desc'];
        $remark_marks = $request['remark_marks'];
        $remark_created_by = $request['remark_created_by'];
        $remark_project_id = $request['remark_project_id'];

        $missingParams = array();

        if (!isset($remark_desc)) {
            $missingParams[] = "remark_desc";
        }
        if (!isset($remark_created_by)) {
            $missingParams[] = "remark_created_by";
        }
        if (!isset($remark_project_id)) {
            $missingParams[] = "remark_project_id";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'remarks';

        $res = $wpdb->insert($table_name, [
            'remark_desc' => $remark_desc,
            'remark_marks' => $remark_marks,
            'remark_created_by' => $remark_created_by,
            'remark_project_id' => $remark_project_id
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error creating remark"), 500);
        }
        return new WP_REST_Response($this->get_response_object(201, "Remark Added Successfully", $remark_project_id), 201);
    }
}
