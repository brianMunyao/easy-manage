<?php

/**
 * @packagejectnager
 */

namespace Inc\Pages;

use Inc\Base\BaseController;
use Inc\Utils\MailHandler;
use WP_Error;
use WP_REST_Response;

class ProjectRoutes extends BaseController
{
    public function register()
    {
        $this->create_projects_table();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/projects', [
            'methods' => "GET",
            'callback' => [$this, 'get_all_projects'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/projects/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_project'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/projects', [
            'methods' => "POST",
            'callback' => [$this, 'create_project'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/projects', [
            'methods' => "PUT",
            'callback' => [$this, 'update_project'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
        register_rest_route('api/v1', '/projects/complete/(?P<project_id>\d+)', [
            'methods' => "PUT",
            'callback' => [$this, 'complete_project'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/projects/trainees/available/(?P<pg_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_available_trainees'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/projects/trainees/(?P<trainee_id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_trainee_projects'],
            'permission_callback' => function () {
                return current_user_can('read');
            }
        ]);
        register_rest_route('api/v1', '/projects/(?P<project_id>\d+)', [
            'methods' => "DELETE",
            'callback' => [$this, 'delete_project'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    public function create_projects_table()
    {
        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';
        $users_table = $wpdb->prefix . 'users';
        $programs_table = $wpdb->prefix . 'programs';

        $sql = "CREATE TABLE IF NOT EXISTS $projects_table (
            project_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            project_name TEXT NOT NULL,
            project_category TEXT NOT NULL,
            project_description TEXT NOT NULL,
            project_due_date DATE NOT NULL DEFAULT CURRENT_DATE,
            project_created_by BIGINT(20) UNSIGNED,
            project_program_id INT NOT NULL,
            project_created_on DATE NOT NULL DEFAULT CURRENT_DATE,
            project_done INT NOT NULL DEFAULT 0,
            CONSTRAINT FK_project_created_by FOREIGN KEY (project_created_by) REFERENCES $users_table(ID),
            CONSTRAINT FK_project_program_id FOREIGN KEY (project_program_id) REFERENCES $programs_table(program_id)
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function get_all_projects($request)
    {
        $trainer_id = $request->get_param('trainer_id');

        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';

        if ($trainer_id) {
            $projects = $wpdb->get_results($wpdb->prepare("SELECT
                    wp_projects.*,
                    GROUP_CONCAT(trainee_id) AS project_assignees
                FROM
                    wp_projects
                LEFT JOIN
                    wp_project_trainees_allocation ON wp_projects.project_id = wp_project_trainees_allocation.project_id
                WHERE
                    wp_projects.project_created_by = $trainer_id
                GROUP BY
                    wp_projects.project_id;"));
        } else {
            $projects = $wpdb->get_results($wpdb->prepare("SELECT * FROM $projects_table"));
        }
        return new WP_REST_Response($this->get_response_object(200, null, $projects));
    }

    public function get_trainee_projects($request)
    {
        $trainee_id = $request->get_param('trainee_id');

        global $wpdb;

        $projects = $wpdb->get_results($wpdb->prepare("SELECT
                wp_projects.*,
                GROUP_CONCAT(trainee_id) AS project_assignees
            FROM
                wp_projects
            LEFT JOIN
                wp_project_trainees_allocation ON wp_projects.project_id = wp_project_trainees_allocation.project_id
            WHERE
            wp_project_trainees_allocation.trainee_id = $trainee_id
            GROUP BY
                wp_projects.project_id;"));

        return new WP_REST_Response($this->get_response_object(200, null, $projects));
    }

    public function get_single_project($request)
    {
        global $wpdb;
        $project = $wpdb->get_row($wpdb->prepare("SELECT
                wp_projects.*,
                GROUP_CONCAT(trainee_id) AS project_assignees
            FROM
                wp_projects
            LEFT JOIN
                wp_project_trainees_allocation ON wp_projects.project_id = wp_project_trainees_allocation.project_id
            WHERE
                wp_projects.project_id = " . $request['id'] . "
            GROUP BY
                wp_projects.project_id;"));

        if (!$project) {
            return new WP_REST_Response($this->get_response_object(404, 'Project does not exist'), 404);
        }
        return new WP_REST_Response($this->get_response_object(200, null, $project));
    }

    public function create_project($request)
    {
        $project_name = $request['project_name'];
        $project_category = $request['project_category'];
        $project_description = $request['project_description'];
        $project_due_date = $request['project_due_date'];
        $project_created_by = $request['project_created_by'];
        $pg_id = $request['project_program_id'];
        $project_assignees = $request['project_assignees'];

        $missingParams = array();

        if (!isset($project_name)) {
            $missingParams[] = "project_name";
        }
        if (!isset($project_category)) {
            $missingParams[] = "project_category";
        }
        if (!isset($project_assignees) || count($project_assignees) == 0) {
            $missingParams[] = "project_assignees";
        }
        if (!isset($project_description)) {
            $missingParams[] = "project_description";
        }
        if (!isset($project_due_date)) {
            $missingParams[] = "project_due_date";
        }
        if (!isset($project_created_by)) {
            $missingParams[] = "project_created_by";
        }
        if (!isset($pg_id)) {
            $missingParams[] = "pg_id";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }


        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';

        $available_trainees = $this->get_available($pg_id);
        $available_trainees = array_values($available_trainees);

        $available_ids = array_column($available_trainees, 'id');

        foreach ($project_assignees as $assigned) {
            if (!in_array($assigned, $available_ids)) {
                $user = get_user_meta($assigned, 'fullname', true);
                return new WP_REST_Response($this->get_response_object(409, $user . " has a maximum value of tasks"), 409);
            }
        }

        $res = $wpdb->insert($table_name, [
            'project_name' => $project_name,
            'project_category' => $project_category,
            'project_description' => $project_description,
            'project_due_date' => $project_due_date,
            'project_created_by' => $project_created_by,
            'project_program_id' => $pg_id
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error creating project"), 500);
        } else {
            $project_id = $wpdb->insert_id;

            foreach ($project_assignees as $assigned) {
                $this->allocate_trainee_to_project($project_id, $assigned);
                // try {

                //     $user = get_user_by('ID', $assigned);
                //     $fullname = get_user_meta($assigned, 'fullname', true);

                //     $mailHandler = new MailHandler();
                //     $email_res = $mailHandler->sendEmail(
                //         $user->user_email,
                //         "Project Assignment - $project_name",
                //         "
                //     Dear $fullname,

                //     We are excited to inform you that you have been assigned to a new project! Here are the details:

                //     Project Name: $project_name
                //     Due Date: " . date('F jS, Y', strtotime($project_due_date)) . "
                //     Project Description: $project_description

                //     Good Luck!

                //     Regards,
                //     Management.
                //     "
                //     );
                // } catch (\Throwable $e) {
                //     return $e;
                // }
            }
            return new WP_REST_Response($this->get_response_object(201, "Project Created Successfully", $project_id), 201);
        }
    }

    public function update_project($request)
    {
        $project_name = $request['project_name'];
        $project_category = $request['project_category'];
        $project_description = $request['project_description'];
        $project_due_date = $request['project_due_date'];
        $pg_id = $request['project_program_id'];
        $project_id = $request['project_id'];
        $project_assignees = $request['project_assignees'];

        $missingParams = array();

        if (!isset($project_name)) {
            $missingParams[] = "project_name";
        }
        if (!isset($project_category)) {
            $missingParams[] = "project_category";
        }
        if (!isset($project_assignees) || count($project_assignees) == 0) {
            $missingParams[] = "project_assignees";
        }
        if (!isset($project_description)) {
            $missingParams[] = "project_description";
        }
        if (!isset($project_due_date)) {
            $missingParams[] = "project_due_date";
        }

        if (!empty($missingParams)) {
            $missingParamsString = implode(", ", $missingParams);
            return new WP_REST_Response($this->get_response_object(400, "Missing parameters: " . $missingParamsString), 400);
        }

        global $wpdb;
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';
        $projects_table = $wpdb->prefix . 'projects';

        $available_trainees = $this->get_available($pg_id);
        $available_trainees = array_values($available_trainees);

        $available_ids = array_column($available_trainees, 'id');

        foreach ($project_assignees as $assigned) {
            if (!in_array($assigned, $available_ids)) {
                $was_assigned = $wpdb->get_row("SELECT id FROM $allocation_table WHERE trainee_id=$assigned AND project_id=$project_id");
                if (!$was_assigned) {
                    $user = get_user_meta($assigned, 'fullname', true);
                    return new WP_REST_Response($this->get_response_object(409, $user . " has a maximum value of tasks"), 409);
                }
            }
        }

        $res = $wpdb->update($projects_table, [
            'project_name' => $request['project_name'],
            'project_category' => $request['project_category'],
            'project_description' => $request['project_description'],
            'project_due_date' => $request['project_due_date'],
        ], [
            'project_id' => $project_id
        ]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error updating project"), 500);
        } else {
            $this->unallocate_trainees_from_project($project_id);

            foreach ($project_assignees as $assigned) {
                $this->allocate_trainee_to_project($project_id, $assigned);
            }
            return new WP_REST_Response($this->get_response_object(200, "Project Updated Successfully"));
        }
    }

    public function get_program_trainees($pg_id)
    {
        global $wpdb;
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


    public function get_available($pg_id)
    {
        $program_trainees = $this->get_program_trainees($pg_id);
        $program_trainees = array_filter($program_trainees, function ($trainee) {
            return $trainee['is_deactivated'] == 0 && $trainee['is_deleted'] == 0;
        });

        global $wpdb;
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';

        $current_allocations = $wpdb->get_results("SELECT trainee_id, COUNT(*) AS project_count FROM $allocation_table GROUP BY trainee_id");

        $unavailable_ids = array();

        foreach ($current_allocations as $row) {
            if ((int)$row->project_count >= 3) {
                array_push($unavailable_ids,  $row->trainee_id);
            }
        }
        $available = array_filter($program_trainees, function ($trainee) use ($unavailable_ids) {
            if (!in_array($trainee['id'], $unavailable_ids)) {
                return $trainee;
            }
        });
        return $available;
    }


    public function get_available_trainees($request)
    {
        $pg_id = $request->get_param('pg_id');
        $res = array_values($this->get_available($pg_id));

        return new WP_REST_Response($this->get_response_object(200, null, $res), 200);
    }

    public function allocate_trainee_to_project($project_id, $trainee_id)
    {
        global $wpdb;
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';

        $res = $wpdb->insert($allocation_table, [
            'project_id' => $project_id,
            'trainee_id' => $trainee_id
        ]);

        if (is_wp_error($res)) {
            return new WP_Error(400, "Error registering to project", $res);
        }

        return $res;
    }

    public function unallocate_trainees_from_project($project_id)
    {
        global $wpdb;
        $allocation_table = $wpdb->prefix . 'project_trainees_allocation';

        $res = $wpdb->delete($allocation_table, ['project_id' => $project_id]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, 'Error removing from project'), 500);
        }

        return $res;
    }

    public function complete_project($request)
    {
        $project_id = $request->get_param('project_id');

        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';
        $tasks_table = $wpdb->prefix . 'tasks';

        $res = $wpdb->update($projects_table, ['project_done' => 1], ['project_id' => $project_id]);

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error deleting project from project"), 500);
        } else {
            $res = $wpdb->update($tasks_table, ['task_done' => 1], ['task_project_id' => $project_id]);
        }

        if (is_wp_error($res)) {
            return new WP_REST_Response($this->get_response_object(500, "Error completing tasks"), 500);
        }
        return new WP_REST_Response($this->get_response_object(200, "Project Completed Sucessfully"), 200);
    }
    public function delete_project($request)
    {
        $project_id = $request->get_param('project_id');

        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';

        try {
            $this->unallocate_trainees_from_project($project_id);
            $res = $wpdb->delete($projects_table, ['project_id' => $project_id]);
            if (is_wp_error($res)) {
                throw new \Exception("Error Processing Request", 1);

                return new WP_REST_Response($this->get_response_object(400, "Error deleting project from project"), 400);
            }
            return new WP_REST_Response($this->get_response_object(200, "Project Deleted", $project_id), 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response($this->get_response_object(400, "Error deleting project from project. " . $e->getMessage()), 400);
        }
    }
}
