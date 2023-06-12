<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

use WP_Error;

class ProjectRoutes
{
    public function register()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('api/v1', '/projects', [
            'methods' => "GET",
            'callback' => [$this, 'get_all_projects'],
            // 'permission_callback' => function () {
            //     return current_user_can('read');
            // }
        ]);
        register_rest_route('api/v1', '/projects/(?P<id>\d+)', [
            'methods' => "GET",
            'callback' => [$this, 'get_single_project'],
            // 'permission_callback' => function () {
            //     return current_user_can('read');
            // }
        ]);
    }

    public function get_all_projects($request)
    {
        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';
        $projects = $wpdb->get_results($wpdb->prepare("SELECT * FROM $projects_table"));

        return $projects;
    }

    public function get_single_project($request)
    {
        global $wpdb;
        $projects_table = $wpdb->prefix . 'projects';
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM $projects_table WHERE project_id=" . $request['id']));

        return $project ?? new WP_Error(404, 'Project does not exist');
    }
}
