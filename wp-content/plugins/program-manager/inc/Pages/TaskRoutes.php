<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use Inc\Base\BaseController;
use Inc\Base\MailHandler;
use WP_REST_Response;

class TaskRoutes extends BaseController
{
    public function register()
    {
        $this->create_tasks_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/tasks/(?P<project_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_project_tasks'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks/single/(?P<task_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks', [
            'methods' => "POST",
            'callback' => [$this, 'create_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks/(?P<task_id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'update_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks/complete/(?P<task_id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'complete_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks/uncomplete/(?P<task_id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'uncomplete_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/tasks/(?P<task_id>\d+)', [
            'methods' => "DELETE",
            'callback' => [$this, 'delete_task'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
    }

    public function create_tasks_table()
    {
        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $projects_table = $wpdb->prefix . 'projects';
        $users_table = $wpdb->prefix . 'users';

        $sql = "CREATE TABLE IF NOT EXISTS $tasks_table (
            task_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            task_name TEXT NOT NULL,
            task_project_id INT NOT NULL,
            task_created_by BIGINT(20) UNSIGNED NOT NULL,
            task_created_on DATE NOT NULL DEFAULT CURRENT_DATE,
            task_done INT NOT NULL DEFAULT 0,
            CONSTRAINT FK_task_project_id FOREIGN KEY (task_project_id) REFERENCES $projects_table(project_id),
            CONSTRAINT FK_task_created_by FOREIGN KEY (task_created_by) REFERENCES $users_table(ID)
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function get_project_tasks($request)
    {
        // $mailHandler  = new MailHandler();
        // $from = 'brianmunyao6@gmail.com';
        // $to = 'brianmunyao6@gmail.com';
        // $subject = 'Subject of the email';
        // $body = 'Content of the email';

        // $result = $mailHandler->sendEmail($from, $to, $subject, $body);
        // if ($result) {
        //     // Return a success response
        //     http_response_code(200);
        //     echo json_encode(['message' => 'Email sent successfully']);
        // } else {
        //     // Return an error response
        //     http_response_code(500);
        //     echo json_encode(['error' => 'Email could not be sent']);
        // }
        $project_id = $request->get_param('project_id');

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $tasks = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tasks_table WHERE task_project_id=$project_id"));

        return new WP_REST_Response($this->get_response_object(200, null, $tasks));
    }

    public function get_single_task($request)
    {
        $task_id = $request->get_param('task_id');

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $task = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tasks_table WHERE task_id=$task_id"));

        if (!$task) {
            return new WP_REST_Response($this->get_response_object(404, "Task $task_id does not exist"), 404);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $task));
    }

    public function create_task($request)
    {
        $task_name = $request['task_name'];
        $task_project_id = $request['task_project_id'];
        $task_created_by = $request['task_created_by'];

        $missingParams = array();

        if (!isset($task_name)) {
            $missingParams[] = "task_name";
        }
        if (!isset($task_project_id)) {
            $missingParams[] = "task_project_id";
        }
        if (!isset($task_created_by)) {
            $missingParams[] = "task_created_by";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $res = $wpdb->insert($tasks_table, [
            'task_name' => $task_name,
            'task_project_id' => $task_project_id,
            'task_created_by' => $task_created_by,
        ]);
        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error creating task"), 500);
        }
        return new WP_REST_Response($this->get_response_object(201, "Task Created Successfully", $wpdb->insert_id), 201);
    }

    public function update_task($request)
    {
        $task_id = $request->get_param('task_id');
        $task_name = $request['task_name'];

        $missingParams = array();

        if (!isset($task_name)) {
            $missingParams[] = "task_name";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $res = $wpdb->update($tasks_table, [
            'task_name' => $task_name,

        ], ['task_id' => $task_id]);

        if (is_wp_error($res) || $res == 0) {
            return new WP_REST_Response($this->get_response_object(500, "Error updating task"), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "Task Updated Successfully"));
    }

    public function complete_task($request)
    {
        $task_id = $request->get_param('task_id');

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $res = $wpdb->update($tasks_table, [
            'task_done' => 1
        ], ['task_id' => $task_id]);

        if (is_wp_error($res) || $res == 0) {
            return new WP_REST_Response($this->get_response_object(500, "Error updating task"), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "Task Updated Successfully"));
    }
    public function uncomplete_task($request)
    {
        $task_id = $request->get_param('task_id');

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $res = $wpdb->update($tasks_table, [
            'task_done' => 0
        ], ['task_id' => $task_id]);

        if (is_wp_error($res) || $res == 0) {
            return new WP_REST_Response($this->get_response_object(500, "Error updating task"), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "Task Updated Successfully"));
    }
    public function delete_task($request)
    {
        $task_id = $request->get_param('task_id');

        global $wpdb;
        $tasks_table = $wpdb->prefix . 'tasks';
        $res = $wpdb->delete($tasks_table, ['task_id' => $task_id]);
        // return $res;

        if ($res == 0) {
            return new WP_REST_Response($this->get_response_object(500, "Error deleting task"), 500);
        }
        return new WP_REST_Response($this->get_response_object(204, "Task Deleted Successfully", $res), 204);
    }
}
