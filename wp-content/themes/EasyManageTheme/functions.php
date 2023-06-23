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
        'p-manager-menu' => __('Program Manager Menu'),
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

// function restrict_wp_admin_access()
// {
//     if (is_admin() && !current_user_can('administrator') && !wp_doing_ajax()) {
//         wp_redirect(home_url());
//         exit;
//     }
// }
// add_action('init', 'restrict_wp_admin_access');


function search_bar_shortcode($attrs)
{
    $att = shortcode_atts([
        'value' => '',
        'placeholder' => 'Employee Search',
        'name' => ''
    ], $attrs);

    return '
        <div class="search-con">
            <ion-icon name="search"></ion-icon>
            <input type="search" name="q" id="search" value="' . $att['value'] . '" placeholder="' . $att['placeholder'] . '">
            <button type="submit" class="search-submit"><ion-icon name="arrow-forward"></ion-icon></button>
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

    $disabled = isset($attrs['disabled']) ? "disabled" : "";

    return '
    <div class="input-con">
        <label for="' . $att['name'] . '">' . $att['label'] . '</label>
        <input type="' . $att['input_type'] . '" name="' . $att['name'] . '" id="' . $att['name'] . '" placeholder="' . $att['placeholder'] . '" value="' . $att['value'] . '" ' . $disabled . '>
        <p class="form-error color-danger">' . $att['error'] . '</p>
    </div>
    ';
}

add_shortcode('input_con', 'input_con_shortcode');

function dash_card_shortcode($attrs)
{
    $att = shortcode_atts([
        'icon' => 'business',
        'label' => '',
        'value' => '',
    ], $attrs);

    return '
    <div class="dash-card">
        <ion-icon name="' . $att['icon'] . '"></ion-icon>

        <p class="dash-number">' . $att['value'] . '</p>
        <p class="dash-label">' . $att['label'] . '</p>
    </div>
    ';
}

add_shortcode('dash_card', 'dash_card_shortcode');


/**
 * Generates a user menu based on the given menu items and highlights the active menu item.
 *
 * @param array $menu_items An array of menu items to be displayed in the user menu.
 * @return void
 */
function get_user_menu($menu_items)
{
    $current_url = get_permalink();
    $res = '';

    foreach ($menu_items as $menu_item) {
        $title = $menu_item->title;
        $base_url = $menu_item->url;
        $is_active = false;

        if ($current_url === $base_url) {
            $is_active = true;
        }

        if (!$is_active && $current_url != home_url() . '/') {
            $child_pages = get_pages(array('child_of' => $menu_item->object_id));
            foreach ($child_pages as $child_page) {
                if (get_permalink($child_page->ID) === $current_url) {
                    $is_active = true;
                    break;
                }
            }
        }

        $res .= '<a href="' . $base_url . '" class="nav-link ' . ($is_active ? "nav-link-active" : "") . '">' . $title . '</a>';
    }
    return $res;
}



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
function get_user_meta_custom($user_id, $key = 'fullname')
{
    return get_user_meta($user_id, $key, true);
}

function get_greeting()
{
    $currentHour = date('G');
    if ($currentHour >= 5 && $currentHour < 12) {
        return 'Good morning';
    } elseif ($currentHour >= 12 && $currentHour < 18) {
        return 'Good afternoon';
    } else {
        return 'Good evening';
    }
}

function format_date($date)
{
    return date('jS F Y', strtotime($date));
}

function sort_by_date_registered($a, $b)
{
    $dateA = strtotime($a->registered_on);
    $dateB = strtotime($b->registered_on);

    if ($dateA == $dateB) {
        return 0;
    }
    return ($dateA > $dateB) ? -1 : 1;
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

function get_initials($name)
{
    $words = explode(' ', $name);
    $initials = '';

    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }

    return $initials;
}

/**
 * 
 * 
 * ===== Input Validation Functions =====
 * 
 */

function validate_email_custom($email)
{
    $email = trim($email);
    if (empty($email)) {
        return "Email is required";
    }
    if (!is_email($email)) {
        return "Invalid email";
    }
    return '';
}

function validate_password_custom($password)
{
    $password = trim($password);
    if (empty($password)) {
        return "Password is required";
    }
    return '';
}

function validate_fullname_custom($fullname)
{
    $fullname = trim($fullname);
    if (empty($fullname)) {
        return "Fullname is required";
    }
    return '';
}


function validate_field_custom($field, $label = "Field")
{
    $field = trim($field);
    if (empty($field)) {
        return $label . " is required";
    }
    return '';
}


function is_response_error($obj)
{
    try {
        if (isset($obj->code)) {
            if (gettype($obj->code) == 'integer') {
                $code = $obj->code;
                if ($code > 300) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return true;
    } catch (\Throwable $err) {
        return true;
    }
}

/**
 * 
 * 
 * ===== Cookie Management =====
 * 
 */
global $token_name;
$token_name = "em_token";

function add_token_cookie($token)
{
    global $token_name;
    $expiration_time = time() + (24 * 60 * 60); // 24 hours
    return setcookie($token_name, $token, $expiration_time, '/easy-manage');
}

function get_token_cookie()
{
    global $token_name;

    if (isset($_COOKIE[$token_name])) {
        return $_COOKIE[$token_name];
    }
    // wp_logout(); // TODO: Find another way to redirect if cookie does not exist
}

function remove_token_cookie()
{
    global $token_name;
    $expiration_time = time() - (60 * 60); // 1 hour ago
    setcookie($token_name, "", $expiration_time, '/easy-manage');
}


/**
 * 
 * 
 * ===== Rest API functions =====
 * 
 */
global $base_url;
$base_url = 'http://localhost/easy-manage/wp-json/api/v1';

global $authHeaders;
$authHeaders = ['Authorization' => 'Bearer ' . get_token_cookie()];

function get_token($email, $password)
{
    global $base_url;

    $res = wp_remote_post($base_url . "/token", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => [
            'email' => $email,
            'password' => $password
        ]
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_program_managers()
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/employees?role=program_manager", [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $program_managers = wp_remote_retrieve_body($res);
    $program_managers = json_decode($program_managers);

    return is_response_error($program_managers) ? [] : $program_managers->data;
}

function get_trainers_new($pm_id = null)
{
    global $base_url;
    global $authHeaders;
    $full_url = $base_url . "/employees?role=trainer";

    if ($pm_id) {
        $full_url .= '?pm_id=' . $pm_id;
    }

    $res = wp_remote_get($full_url, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $trainers = wp_remote_retrieve_body($res);
    $trainers =  json_decode($trainers);

    return is_response_error($trainers) ? [] : $trainers->data;
}

function get_trainees_new($trainer_id = NULL, $program_id = NULL)
{
    global $base_url;
    global $authHeaders;
    $full_url = $base_url . "/employees?role=trainee";

    $res = wp_remote_get($full_url, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $trainees = wp_remote_retrieve_body($res);
    $trainees = json_decode($trainees);

    return is_response_error($trainees) ? [] : $trainees->data;
}

function get_employees_new()
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/employees", [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $employees = wp_remote_retrieve_body($res);
    $employees = json_decode($employees);

    return is_response_error($employees) ? [] : $employees->data;
}

function get_single_employees_new($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/employees/" . $id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $employee = wp_remote_retrieve_body($res);
    return json_decode($employee);
}

function search_employees($q)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/employees/search?q=" . $q, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $results = wp_remote_retrieve_body($res);
    $results = json_decode($results);

    return is_response_error($results) ? [] : $results->data;
}

function get_users_created_by($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/employees/created_by/" . $id . "?role=trainer", [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $users = wp_remote_retrieve_body($res);
    $users = json_decode($users);

    return is_response_error($users) ? [] : $users->data;
}

function create_employee_new($user)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => $user,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function update_employee_new($user)
{
    $user_id = $user['id'];
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees/$user_id", [
        'method' => 'PUT',
        'data_format' => 'body',
        'body' => $user,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function activate_employee($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees/activate/" . $id, [
        'method' => 'PUT',
        'data_format' => 'body',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function deactivate_employee($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees/deactivate/" . $id, [
        'method' => 'PUT',
        'data_format' => 'body',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function delete_employee($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees/delete/" . $id, [
        'method' => 'PUT',
        'data_format' => 'body',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function restore_employee($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/employees/restore/" . $id, [
        'method' => 'PUT',
        'data_format' => 'body',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_programs_new($pmanager_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/programs/" . $pmanager_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function get_single_program_new($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/programs/single/" . $id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);
    $program = wp_remote_retrieve_body($res);
    return json_decode($program);
}
function get_program_assignee($id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/programs/assigned_to/" . $id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);
    $program = wp_remote_retrieve_body($res);
    return json_decode($program);
}
function create_program_new($program)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/programs", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => $program,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function get_unassigned_programs_new($pmanager_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/programs/unassigned/" . $pmanager_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);
    $res = wp_remote_retrieve_body($res);
    $res = json_decode($res);

    return is_response_error($res) ? [] : $res->data;
}

function allocate_program($trainer_id, $program_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/programs/allocate", [
        'method' => 'PUT',
        'data_format' => 'body',
        'body' => [
            "trainer_id" => $trainer_id,
            "program_id" => $program_id
        ],
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function update_program_new($program)
{
    $program_id = $program['program_id'];
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/programs/$program_id", [
        'method' => 'PUT',
        'data_format' => 'body',
        'body' => $program,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_all_projects($trainer_id = NULL)
{
    global $authHeaders;
    global $base_url;
    $full_url = $base_url . "/projects";

    if ($trainer_id) {
        $full_url .= '?trainer_id=' . $trainer_id;
    }

    $res = wp_remote_get($full_url, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $projects = wp_remote_retrieve_body($res);
    $projects = json_decode($projects);

    return is_response_error($projects) ? [] : $projects->data;
}

function get_single_project_new($p_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/projects/" . $p_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $project = wp_remote_retrieve_body($res);
    return json_decode($project);
}

function assign_trainee_to_program($trainee_id, $program_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/trainees/allocate-program", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => [
            'trainee_id' => $trainee_id,
            'program_id' => $program_id
        ],
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_trainees_in_program($program_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/programs/trainees/" . $program_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $trainees = wp_remote_retrieve_body($res);
    $trainees = json_decode($trainees);

    return is_response_error($trainees) ? [] : $trainees->data;
}
function get_trainees_projects($trainee_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/projects/trainees/" . $trainee_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $projects = wp_remote_retrieve_body($res);
    $projects = json_decode($projects);

    return is_response_error($projects) ? [] : $projects->data;
}

function create_project($project)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/projects", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => $project,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function update_project($project)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/projects", [
        'method' => 'PUT',
        'data_format' => 'body',
        'body' => $project,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_available_trainees($program_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/projects/trainees/available/" . $program_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    $res = json_decode($res);

    return is_response_error($res) ? [] : $res->data;
}

function complete_project($project_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/projects/complete/" . $project_id, [
        'method' => 'PUT',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function delete_project($project_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/projects/" . $project_id, [
        'method' => 'DELETE',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function get_tasks($project_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/tasks/" . $project_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    $res = json_decode($res);

    return is_response_error($res) ? [] : $res->data;
}
function get_single_task($task_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_get($base_url . "/tasks/single/" . $task_id, [
        'method' => 'GET',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function create_task($task)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/tasks", [
        'method' => 'POST',
        'data_format' => 'body',
        'body' => $task,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

function update_task($task, $task_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/tasks/" . $task_id, [
        'method' => 'PUT',
        'data_format' => 'body',
        'body' => $task,
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function complete_task($task_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/tasks/complete/" . $task_id, [
        'method' => 'PUT',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function uncomplete_task($task_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/tasks/uncomplete/" . $task_id, [
        'method' => 'PUT',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}
function delete_task($task_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/tasks/" . $task_id, [
        'method' => 'DELETE',
        'headers' => $authHeaders
    ]);

    $res = wp_remote_retrieve_body($res);
    return json_decode($res);
}

/**
 * 
 *
 * ===== Custom Actions =====
 * 
 */

function on_project_delete()
{
    wp_redirect(site_url('/projects'));
    exit();
}
add_action('on_project_delete', 'on_project_delete');

// function move_to_previous_page()
// {
//     wp_redirect(wp_get_referer());
//     exit();
// }

// add_action('move_to_previous_page', 'move_to_previous_page');


/**
 * 
 * 
 * ===== Login Limits =====
 * 
 */

function check_login_attempts($user, $username, $password)
{
    $attempted_login = get_transient('attempted_login');
    $max_attempts = 3;
    $transient_timeout = get_option('_transient_timeout_attempted_login');

    if ($attempted_login && $attempted_login['tried'] >= $max_attempts) {
        $time_left = calculate_time_left($transient_timeout);

        $error_message = 'Too many attempts. Try again in ' . $time_left;

        return new WP_Error('too_many_tried', $error_message);
    }

    return $user;
}

add_filter('authenticate', 'check_login_attempts', 30, 3);

function increment_login_attempts($username)
{
    $attempted_login = get_transient('attempted_login') ?: ['tried' => 0];
    $max_attempts = 3;
    $expiration_time = 60;

    $attempted_login['tried']++;
    if ($attempted_login['tried'] <= $max_attempts) {
        set_transient('attempted_login', $attempted_login, $expiration_time);
    }
}

add_action('wp_login_failed', 'increment_login_attempts', 10, 1);

function calculate_time_left($timestamp)
{
    $periods = [
        "second" => 60,
        "minute" => 60,
        "hour" => 24,
        "day" => 7,
        "week" => 4.35,
        "month" => 12
    ];

    $currentTimestamp = time();
    $difference = abs($currentTimestamp - $timestamp);
    $periodKeys = array_keys($periods);
    $periodCount = count($periods);

    for ($i = 0; $i < $periodCount && $difference >= $periods[$periodKeys[$i]]; $i++) {
        $difference /= $periods[$periodKeys[$i]];
    }

    $difference = round($difference);
    $period = $periodKeys[$i];

    if ($difference !== 1) {
        $period .= "s";
    }

    return ($difference !== 0) ? "$difference $period" : null;
}
