<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

class CreateTables
{
    public function register()
    {
        $this->create_projects_table();
    }
    public function create_projects_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'projects';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            project_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            project_name TEXT NOT NULL,
            project_category TEXT NOT NULL,
            project_description TEXT NOT NULL,
            project_due_date DATE NOT NULL DEFAULT CURRENT_DATE,
            project_assignees TEXT NOT NULL,
            project_created_by INT NOT NULL,
            project_program_id INT NOT NULL,
            project_created_on DATE NOT NULL DEFAULT CURRENT_DATE,
            project_done INT NOT NULL DEFAULT 0
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
