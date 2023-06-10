<?php


function custom_enqueue_scripts()
{
    wp_enqueue_style('style', get_template_directory_uri() . '/style.css', [], '1.0.0', 'all');
    wp_enqueue_style('projects_styles', get_template_directory_uri() . '/styles/projects.css', [], '1.0.0', 'all');
}

add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

add_theme_support('custom-logo');
add_theme_support('menus');

function register_theme_menus()
{
    register_nav_menus([
        'admin-menu' => __('Admin Menu'),
        'pm-menu' => __('Program Manager Menu'),
        'trainer-menu' => __('Trainer Menu'),
        'trainee-menu' => __('Trainee Menu')
    ]);
}
add_action('after_setup_theme', 'register_theme_menus');

function redirect_on_login()
{
    wp_redirect(home_url());
    exit();
}
add_action('wp_login', 'redirect_on_login');

function redirect_on_logout()
{
    wp_redirect(site_url('/login'));
    exit();
}
add_action('wp_logout', 'redirect_on_logout');

function search_bar_shortcode($attrs)
{
    $att = shortcode_atts([
        'value' => '',
        'placeholder' => 'Quick Employee Search'
    ], $attrs);

    return '
        <div class="search-con">
            <ion-icon name="search"></ion-icon>
            <input type="search" name="search" id="search" value="' . $att['value'] . '" placeholder="' . $att['placeholder'] . '">
            <!-- <button type="submit" class="app-btn">Search</button> --> 
        </div>
    ';
}

add_shortcode('search_bar', 'search_bar_shortcode');

function input_con_shortcode($attrs)
{
    $att = shortcode_atts([
        'name' => '',
        'label' => '',
        'value' => '',
        'placeholder' => '',
        'error' => '',
        'input_type' => 'text'
    ], $attrs);

    return '
    <div class="input-con">
        <label for="' . $att['name'] . '">' . $att['label'] . '</label>
        <input type="' . $att['input_type'] . '" name="' . $att['name'] . '" id="' . $att['name'] . '" placeholder="' . $att['placeholder'] . '" value="' . $att['value'] . '">
        <p class="form-error color-danger">' . $att['error'] . '</p>
    </div>
    ';
}



add_shortcode('input_con', 'input_con_shortcode');


/**
 * Returns the role of the currently logged in user.
 *
 * @return string|false The role of the currently logged in user, or false if the user is not logged in.
 */
function get_user_role()
{
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

        if (!empty($user_roles)) {
            $user_role = array_shift($user_roles);
            return $user_role;
        }
    }
    return false;
}

/**
 * Checks if the current user is an administrator.
 *
 * @return bool
 */
function is_user_admin_custom()
{
    return get_user_role() == 'administrator';
}


/**
 * 
 * Rest API functions
 */

global $projects;
$projects = [
    (object)[
        'id' => 1,
        'name' => 'Project A',
        'category' => 'Web App',
        'progress' => 80,
        'due_date' => '2023-07-15',
        'done' => 0
    ],
    (object)[
        'id' => 2,
        'name' => 'Project B',
        'category' => 'Mobile App',
        'progress' => 100,
        'due_date' => '2023-08-31',
        'done' => 1
    ],
    (object)[
        'id' => 3,
        'name' => 'Project C',
        'category' => 'Desktop App',
        'progress' => 20,
        'due_date' => '2023-06-30',
        'done' => 0
    ]
];

$tasks = [
    (object)[
        'id' => 1,
        'name' => 'Task 1',
        'done' => 1,
        'project_id' => 1,
    ],
    (object)[
        'id' => 2,
        'name' => 'Task 2',
        'done' => 0,
        'project_id' => 2,
    ],
    (object)[
        'id' => 3,
        'name' => 'Task 3',
        'done' => 0,
        'project_id' => 1,
    ],
    (object)[
        'id' => 4,
        'name' => 'Task 4',
        'done' => 1,
        'project_id' => 3,
    ],
    (object)[
        'id' => 5,
        'name' => 'Task 5',
        'done' => 0,
        'project_id' => 2,
    ],
];



function get_projects()
{
    global $projects;

    return $projects;
}

function get_single_project($id)
{
    global $projects;

    $filtered_projects = array_filter($projects, function ($project) use ($id) {
        return $project->id == $id;
    });

    $matched_project = reset($filtered_projects);

    if ($matched_project) {
        return $matched_project;
    }
    return false;
}

function get_tasks($id)
{
    global $tasks;
    $filtered_tasks = array_filter($tasks, function ($task) use ($id) {
        return $task->project_id == $id;
    });

    return $filtered_tasks;
}

function get_single_task($id)
{
    global $tasks;

    $filtered_tasks = array_filter($tasks, function ($project) use ($id) {
        return $project->id == $id;
    });

    $matched_project = reset($filtered_tasks);

    if ($matched_project) {
        return $matched_project;
    }
    return false;
}
