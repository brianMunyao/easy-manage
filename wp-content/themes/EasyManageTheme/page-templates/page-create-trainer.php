<?php
if (!is_user_p_manager()) {
    wp_redirect(home_url());
}
$fullname_error = '';
$email_error = '';
$password_error = '';
$program_error = '';

$form_error = '';
$form_success = '';

if (isset($_POST['create-trainer'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $program = isset($_POST['program']) ? $_POST['program'] : '';

    $fullname_error = validate_fullname_custom($fullname);
    $email_error = validate_email_custom($email);
    $password_error = validate_password_custom($password);
    // $program_error = empty($program) ? 'Program is required' : '';

    if (empty($fullname_error) && empty($email_error) && empty($password_error)) {
        $result = create_trainer([
            'fullname' => $fullname,
            'email' => $email,
            'password' => $password,
            'role' => 'trainer',
            'is_deactivated' => 0,
            'is_deleted' => 0,
            'created_by' => get_current_user_id()
        ], $program);


        //TODO: implement good error checking
        $form_success = "Successfully created";
    }
}

/**
 * 
 * Template Name: Create Trainer Page Template
 */
get_header() ?>

<div class="app-padding create-trainer-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/trainers') ?>'>/ Trainers</a>
        <span>/ Create Trainer</span>
    </div>

    <?php
    $programs = get_unassigned_programs(get_current_user_id());
    ?>

    <div class="form-container">
        <form action="" method="POST">
            <div class="form">
                <h2>Create Trainer</h2>

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

                <div class="input-con">
                    <!-- <div> -->
                    <label for="program">Training Program</label>
                    <select name="program" id="program">
                        <option value="" selected disabled hidden>Select a training</option>
                        <?php
                        foreach ($programs as $pg) {
                        ?>
                            <option value="<?php echo $pg->id ?>"><?php echo $pg->program_name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <!-- </div> -->
                    <p class="form-error color-danger"><?php echo $program_error ?></p>
                </div>

                <!-- <p class="form-info">
                    <ion-icon name="information-circle-outline"></ion-icon>
                    NOTE: You need to create a program first so as to assign it
                </p> -->

                <button type="submit" class="app-btn primary-btn" name="create-trainer">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>