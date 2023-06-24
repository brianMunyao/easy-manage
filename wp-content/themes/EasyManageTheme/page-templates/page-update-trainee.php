<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
$id = $_GET['id'];

$user_info = get_single_employees_new($id);
if (is_response_error($user_info)) wp_redirect('/trainees');
$user_info = $user_info->data;

$fullname_error = $password_error = '';
$form_error = $form_success = '';

if (isset($_POST['update-trainee'])) {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];

    $fullname_error = validate_fullname_custom($fullname);
    $password_error = validate_password_custom($password);

    if (empty($fullname_error)  && empty($password_error)) {
        $result = update_employee_new([
            'id' => $user_info->id,
            'fullname' => $fullname,
            'password' => $password,
        ]);



        if (is_response_error($result)) {
            $form_error = $result->message;
        } else {
            $form_success = "Successfully updated";
        }
    }
}


/**
 * 
 * Template Name: Update Trainee Page Template
 */
get_header() ?>

<div class="app-padding update-trainee-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/trainees') ?>'>/ Trainees</a>
        <span>/ Update Trainee</span>
    </div>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Trainee</h2>

                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>

                <?php
                $curr_fullname = $_POST["fullname"] ?? $user_info->fullname;
                $curr_password = $_POST["password"] ?? "";
                ?>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="' . $fullname_error . '" placeholder="Enter their fullname" value="' . $curr_fullname . '"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" placeholder="Enter their email address" input_type="email" value="' . $user_info->email . '" disabled="true"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $password_error . '" placeholder="Enter their password" input_type="password" value="' . $curr_password . '"]') ?>

                <button type="submit" class="app-btn primary-btn" name="update-trainee">Update</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>