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
function is_user_p_manager()
{
    return get_user_role() == 'program_manager';
}
function is_user_trainer()
{
    return get_user_role() == 'trainer';
}
function is_user_trainee()
{
    return get_user_role() == 'trainee';
}

function format_date($date)
{
    return date('jS F Y', strtotime($date));;
}


/**
 * 
 * Rest API functions
 */


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
    $res = wp_remote_get("http://localhost:3000/projects", [
        'method' => 'GET',
        // 'headers' => ['Authorization' => 'Bearer ' . $GLOBALS['token']]
    ]);
    $projects = wp_remote_retrieve_body($res);
    return json_decode($projects);
}

function get_single_project($id)
{
    $res = wp_remote_get("http://localhost:3000/projects?project_id=" . $id, [
        'method' => 'GET',
        // 'headers' => ['Authorization' => 'Bearer ' . $GLOBALS['token']]
    ]);
    $projects = wp_remote_retrieve_body($res);
    $projects = json_decode($projects);

    $project = reset($projects);
    return $project;
}

function get_tasks($id)
{
    $res = wp_remote_get("http://localhost:3000/tasks", [
        'method' => 'GET',
        // 'headers' => ['Authorization' => 'Bearer ' . $GLOBALS['token']]
    ]);
    $tasks = wp_remote_retrieve_body($res);
    $tasks = json_decode($tasks);

    $filtered_tasks = array_filter($tasks, function ($task) use ($id) {
        return $task->task_project_id == $id;
    });

    return $filtered_tasks;
}

function get_single_task($id)
{
    $res = wp_remote_get("http://localhost:3000/tasks?task_id=" . $id, [
        'method' => 'GET',
        // 'headers' => ['Authorization' => 'Bearer ' . $GLOBALS['token']]
    ]);
    $tasks = wp_remote_retrieve_body($res);
    $tasks = json_decode($tasks);

    $task = reset($tasks);
    return $task;
}

function calculate_completion_percentage($arr1, $arr2)
{
    $res = "100% 0%";
    if (count($arr2) > 0) {

        $ongoing_percentage = (count($arr1) / count(array_merge($arr1, $arr2))) * 100;
        $completed_percentage = 100 - $ongoing_percentage;

        $res  = "{$ongoing_percentage}% {$completed_percentage}%";
    }
    return $res;
}

function calculate_percentage($completed, $total)
{
    $res = "0%";
    if (count($total) > 0) {
        $percentage = (count($completed) / count($total)) * 100;
        $res = ceil($percentage) . "%";
    }
    return $res;
}
