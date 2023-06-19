<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (!is_user_p_manager()) {
    wp_redirect(home_url());
}
if (!isset($_GET['id'])) {
    wp_redirect(site_url('/employees'));
}
$id = $_GET['id'];

$user_info = get_single_employees_new($id);
$assigned_cohort = get_program_assignee($id);

if (is_response_error($assigned_cohort)) {
    $assigned_cohort = NULL;
}

$fullname_error = $password_error = '';

$form_error = $form_success = '';


if (isset($_POST['update-trainer'])) {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $program = isset($_POST['program']) ? $_POST['program'] : '';

    $fullname_error = validate_fullname_custom($fullname);
    $password_error = validate_password_custom($password);

    if (empty($fullname_error)  && empty($password_error)) {
        $result = update_employee_new([
            'id' => $user_info->id,
            'fullname' => $fullname,
            'password' => $password,
        ]);

        if (!empty($program)) {
            $result = allocate_program($user_info->id, $program);
        }

        if (is_response_error($result)) {
            $form_error = $result->message;
        } else {
            $form_success = "Successfully updated";
        }
    }
}

/**
 * 
 * Template Name: Update Trainer Page Template
 */
get_header() ?>

<div class="app-padding update-trainer-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/trainers') ?>'>/ Trainers</a>
        <span>/ Update Trainer</span>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Trainer</h2>

                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>

                <?php
                $curr_fullname = $_POST["fullname"] ?? $user_info->fullname;
                $curr_password = $_POST["password"] ?? "";
                ?>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="' . $fullname_error . '" placeholder="Enter their fullname" value="' . $curr_fullname . '"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" placeholder="Enter their email address" input_type="email" value="' . $user_info->email . '" disabled="true"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $password_error . '" placeholder="Enter their password" input_type="password" value="' . $curr_password . '"]') ?>

                <div class="input-con">
                    <div>
                        <label for="program">Training Program</label>
                        <select name="program" id="program">
                            <option value="" selected disabled hidden>Select a training</option>
                            <?php
                            $unassigned_programs = get_unassigned_programs_new(get_current_user_id());
                            array_push($unassigned_programs, $assigned_cohort);

                            foreach ($unassigned_programs as $program) {
                            ?>
                                <option value="<?php echo $program->program_id ?>" <?php echo isset($assigned_cohort) ? ($assigned_cohort->program_id == $program->program_id ? "selected" : "") : '' ?>><?php echo $program->program_name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="form-error color-danger"></p>
                </div>

                <button type="submit" class="app-btn primary-btn" name="update-trainer">Update</button>
            </div>
        </form>
    </div>
</div>
<?php get_footer() ?>