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


/**
 * 
 * 
 * ===== SHORT CODES =====
 * 
 */


function identicon_shortcode($attrs)
{
    $att = shortcode_atts(['str' => 'default'], $attrs);

    $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($att['str']))) . '?d=identicon';

    return "<img src='$avatar' alt='avatar'>";
}
add_shortcode('identicon', 'identicon_shortcode');


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
        <div class="icon">
            <ion-icon name="' . $att['icon'] . '"></ion-icon>
        </div>

        <p class="dash-label">' . $att['label'] . '</p>
        <p class="dash-number">' . $att['value'] . '</p>
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
/**
 * Checks if the current user has the role of program manager.
 *
 * @return bool
 */
function is_user_p_manager()
{
    return get_user_role() == 'program_manager';
}
/**
 * Checks if the current user has the role of a trainer.
 *
 * @return bool
 */
function is_user_trainer()
{
    return get_user_role() == 'trainer';
}
/**
 * Checks if the current user has the role of trainee.
 *
 * @return bool
 */
function is_user_trainee()
{
    return get_user_role() == 'trainee';
}
/**
 * Retrieve a specific meta data field for a user.
 *
 * @param int $user_id The ID of the user.
 * @param string $key The meta data field key.
 * @return mixed The value of the meta data field.
 */
function get_user_meta_custom($user_id, $key = 'fullname')
{
    return get_user_meta($user_id, $key, true);
}

/**
 * Returns a greeting based on the current time of day.
 *
 * @return string
 */
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

/**
 * Formats a date string into a good format.
 *
 * @param string $date The date string to format.
 * @return string The formatted date string.
 */
function format_date($date)
{
    return date('jS F Y', strtotime($date));
}

/**
 * Sorts an array of objects by their date of registration in descending order.
 *
 * @param  object  $a
 * @param  object  $b
 * @return int
 */
function sort_by_date_registered($a, $b)
{
    $dateA = strtotime($a->registered_on);
    $dateB = strtotime($b->registered_on);

    if ($dateA == $dateB) {
        return 0;
    }
    return ($dateA > $dateB) ? -1 : 1;
}

/**
 * Calculates the completion percentage of projects.
 *
 * @param array $arr1 The array containing ongoing tasks.
 * @param array $arr2 The array containing completed tasks.
 * @return string The completion percentage in the format of "ongoing% completed%".
 */
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

/**
 * Calculates the percentage of completed items out of total items.
 *
 * @param array $completed The number of completed items.
 * @param array $total The total number of items.
 * @return string The percentage of completed items out of total items.
 */
function calculate_percentage($completed, $total)
{
    $res = "0%";
    if (count($total) > 0) {
        $percentage = (count($completed) / count($total)) * 100;
        $res = ceil($percentage) . "%";
    }
    return $res;
}

/**
 * Returns the initials of a given name.
 *
 * @param string $name The name to get the initials from.
 * @return string The initials of the name.
 */
function get_initials($name)
{
    $words = explode(' ', $name);
    $initials = '';

    foreach (array_slice($words, 0, 2) as $word) {
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

/**
 * Validates an email address 
 *
 * @param string $email The email address to validate.
 * @return string An error message if the email is invalid or empty, otherwise an empty string.
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

/**
 * Validates a password string.
 *
 * @param string $password The password string to validate.
 * @return string Returns an empty string if the password is valid, otherwise returns an error message.
 */
function validate_password_custom($password)
{
    $password = trim($password);
    if (empty($password)) {
        return "Password is required";
    }
    return '';
}

/**
 * Validates a full name string.
 *
 * @param string $fullname The full name string to be validated.
 * @return string Returns an error message if the full name is empty, otherwise returns an empty string.
 */
function validate_fullname_custom($fullname)
{
    $fullname = trim($fullname);
    if (empty($fullname)) {
        return "Fullname is required";
    }
    return '';
}


/**
 * Validates a given field and returns an error message if it is empty.
 *
 * @param string $field The field to validate.
 * @param string $label The label to use in the error message.
 * @return string Returns an error message if the field is empty, otherwise an empty string.
 */
function validate_field_custom($field, $label = "Field")
{
    $field = trim($field);
    if (empty($field)) {
        return $label . " is required";
    }
    return '';
}


/**
 * Determines if the given object is an error response.
 *
 * @param mixed $obj The object to check.
 * @return bool Returns true if the object is an error response, false otherwise.
 */
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

/**
 * Adds a token cookie to the user's browser.
 * @global string $token_name
 * @param string $token The token to be added to the cookie.
 * @return bool Returns true if the cookie was successfully set, false otherwise.
 */
function add_token_cookie($token)
{
    global $token_name;
    $expiration_time = time() + (24 * 60 * 60); // 24 hours
    return setcookie($token_name, $token, $expiration_time, '/easy-manage');
}

/**
 * Retrieves the value of the token cookie, if it exists.
 *
 * @global string $token_name
 * @return string The value of the token cookie, or an empty string if it does not exist.
 */
function get_token_cookie()
{
    global $token_name;

    if (isset($_COOKIE[$token_name])) {
        return $_COOKIE[$token_name];
    }
    return "";
    // wp_logout(); // TODO: Find another way to redirect if cookie does not exist
}

/**
 * Removes the token cookie.
 *
 * @global string $token_name
 * @return void
 */
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

/**
 * Retrieves a token from the API using the provided email and password.
 *
 * @param string $email The email of the user.
 * @param string $password The password of the user.
 * @return mixed|null Returns the token as a JSON object or null if the request fails.
 */
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

/**
 * Retrieves all program managers from the API endpoint.
 *
 * @return array An array of program managers.
 */
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

    return is_response_error($program_managers) ? [] : $program_managers->data ?? [];
}

/**
 * Retrieves a list of trainers from the API.
 *
 * @param int|null $pm_id - Optional project manager ID to filter trainers by.
 * @return array - An array of trainers, or an empty array if there was an error.
 */
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

    return is_response_error($trainers) ? [] : $trainers->data ?? [];
}

/**
 * Retrieves the list of trainees filtered by trainer and program.
 *
 * @param int|null $trainer_id
 * @param int|null $program_id
 * @return array
 */
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

    return is_response_error($trainees) ? [] : $trainees->data ?? [];
}

/**
 * Retrieves the list of employees from the API endpoint.
 *
 * @return array The list of employees retrieved from the API endpoint.
 */
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

    return is_response_error($employees) ? [] : $employees->data ?? [];
}

/**
 * Retrieves a single employee from the API using the provided ID.
 *
 * @param int $id The ID of the employee to retrieve.
 * @return object Returns the employee object if found, error object otherwise.
 */
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

/**
 * Search for employees based on the given query string.
 *
 * @param string $q The query string to search for.
 * @return array An array of employee objects matching the query string.
 */
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

    return is_response_error($results) ? [] : $results->data ?? [];
}

/**
 * Retrieves the users created by the given ID.
 *
 * @param int $id The ID of the user who created the employees.
 * @return array An array of users created by the given ID.
 */
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

    return is_response_error($users) ? [] : $users->data ?? [];
}

/**
 * Creates a new employee by sending a POST request to the API endpoint.
 *
 * @param array $user An array containing the details of the employee to be created.
 * @return object The response from the API endpoint in JSON format.
 */
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

/**
 * Updates an employee with the given user data.
 *
 * @param array $user An array containing the user data to update.
 * @return object Returns an object with employee id if successful, or an error object if the update failed.
 */
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
    $res = json_decode($res);

    return is_response_error($res) ? [] : $res->data ?? [];
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

    return is_response_error($res) ? [] : $res->data ?? [];
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
function unallocate_program($trainer_id)
{
    global $base_url;
    global $authHeaders;

    $res = wp_remote_post($base_url . "/programs/unallocate/" . $trainer_id, [
        'method' => 'PUT',
        'data_format' => 'body',
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

    return is_response_error($projects) ? [] : $projects->data ?? [];
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

    return is_response_error($trainees) ? [] : $trainees->data ?? [];
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

    return is_response_error($projects) ? [] : $projects->data ?? [];
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

    return is_response_error($res) ? [] : $res->data ?? [];
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

    return is_response_error($res) ? [] : $res->data ?? [];
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

/**
 * Checks the number of login attempts for a user and returns an error message if the maximum number of attempts has been reached.
 *
 * @param mixed $user
 * @param string $username
 * @param string $password
 * @return mixed|WP_Error
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

/**
 * Increments the login attempts for a given username.
 *
 * @param string $username The username for which to increment the login attempts.
 * @return void
 */
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

/**
 * Calculates the time left between the given timestamp and the current time.
 *
 * @param int $timestamp The timestamp to calculate the time left from.
 * @return string|null Returns a string representing the time left or null if the difference is zero.
 */
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
