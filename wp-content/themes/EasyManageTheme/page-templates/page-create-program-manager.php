<?php
$fullname_error = '';
$email_error = '';
$password_error = '';

$form_error = '';
$form_success = '';


if (isset($_POST['create-pm'])) {
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
            'role' => 'program_manager',
            'is_deactivated' => 0,
            'is_deleted' => 0,
            'created_by' => get_current_user_id()
        ]);

        //TODO: implement good error checking
        $form_success = "Successfully created";
    }
}



/**
 * 
 * Template Name: Create Program Manager Page Template
 */
get_header() ?>

<div class="app-padding create-program-manager-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/employees') ?>'>/ Employees</a>
        <span>/ Create Project Manager</span>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Create Program Manager</h2>

                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>
                <?php
                $curr_fullname = $_POST["fullname"] ?? "";
                $curr_email = $_POST["email"] ?? "";
                $curr_password = $_POST["password"] ?? "";
                ?>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="' . $fullname_error . '" placeholder="Enter their fullname" value="' . $curr_fullname . '"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" error="' . $email_error . '" placeholder="Enter their email address" input_type="email" value="' . $curr_email . '"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $password_error . '" placeholder="Enter their password" input_type="password" value="' . $curr_password . '"]') ?>

                <button type="submit" class="app-btn primary-btn" name="create-pm">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>