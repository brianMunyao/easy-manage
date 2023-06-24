<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php if (!is_user_trainer()) wp_redirect(home_url()) ?>

<?php

$fullname_error = $email_error = $password_error = '';
$form_error = $form_success = '';

if (isset($_POST['create-trainee'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $fullname_error = validate_fullname_custom($fullname);
    $email_error = validate_email_custom($email);
    $password_error = validate_password_custom($password);

    if (empty($fullname_error) && empty($email_error) && empty($password_error)) {
        $result = create_employee_new([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $password,
            'role' => 'trainee',
            'is_deactivated' => 1,
            'is_deleted' => 0,
            'created_by' => get_current_user_id()
        ]);

        if (is_response_error($result)) {
            $form_error = $result->message ?? "Creation Failed";
        } else {
            $result = $result->data; // ? New trainee id

            $assigned_program = get_program_assignee(get_current_user_id());
            if (!is_response_error($assigned_program)) {
                $assigned_program = $assigned_program->data; // ? current program object
                $result = assign_trainee_to_program($result, $assigned_program->program_id);

                if (is_response_error($result)) {
                    $form_error = $result->message ?? "Creation Failed";
                } else {
                    $form_success = "Successfully Created";
                }
            } else {
                $form_error = $result->message ?? "Creation Failed";
            }
        }
    }
}


/**
 * 
 * Template Name: Create Trainee Page Template
 */
get_header() ?>

<div class="app-padding create-trainee-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/trainees') ?>'>/ Trainees</a>
        <span>/ Create Trainee</span>
    </div>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Create Trainee</h2>

                <p class="success"><?php echo $form_success ?></p>
                <p class="error"><?php echo $form_error ?></p>

                <?php
                $curr_fullname = $_POST["fullname"] ?? "";
                $curr_email = $_POST["email"] ?? "";
                $curr_password = $_POST["password"] ?? "";
                ?>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="' . $fullname_error . '" placeholder="Enter their fullname" value="' . $curr_fullname . '"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" error="' . $email_error . '" placeholder="Enter their email address" input_type="email" value="' . $curr_email . '"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $password_error . '" placeholder="Enter their password" input_type="password" value="' . $curr_password . '"]') ?>

                <button type="submit" class="app-btn primary-btn" name="create-trainee">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>