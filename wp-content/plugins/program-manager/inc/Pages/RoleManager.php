<?php

/**
 * @package ProgramManager
 */

namespace Inc\Pages;

class RoleManager
{
    public function register()
    {
        // Add Program Manager role
        add_role('program_manager', 'Program Manager', [
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => true,
            'publish_posts' => true
        ]);

        // Add Trainer role
        add_role('trainer', 'Trainer', [
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => true
        ]);

        // Add Trainee role
        add_role('trainee', 'Trainee', [
            'read' => true
        ]);
    }
}
