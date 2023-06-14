<?php
if (!is_user_admin_custom()) {
    wp_redirect(home_url());
}
if (!isset($_GET['id'])) {
    wp_redirect(site_url('/employees'));
}
$id = $_GET['id'];

$user_info = get_single_employee($id);

$fullname_error = '';
$password_error = '';

$form_error = '';
$form_success = '';


if (isset($_POST['update-pm'])) {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];

    $fullname_error = validate_fullname_custom($fullname);
    $password_error = validate_password_custom($password);

    if (empty($fullname_error)  && empty($password_error)) {
        $result = update_employee([
            'id' => $user_info->id,
            'fullname' => $fullname,
            'email' => $user_info->email,
            'password' => $password,
            'role' => $user_info->role,
            'is_deactivated' => $user_info->is_deactivated,
            'is_deleted' => $user_info->is_deleted
        ]);

        //TODO: implement good error checking
        $form_success = "Successfully updated";
    }
}

/**
 * 
 * Template Name: Update Program Manager Page Template
 */
get_header() ?>

<div class="app-padding update-program-manager-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/employees') ?>'>/ Employees</a>
        <span>/ Update Project Manager</span>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Program Manager</h2>

                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="' . $fullname_error . '" placeholder="Enter their fullname" value="' . $user_info->fullname . '"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" placeholder="Enter their email address" input_type="email" value="' . $user_info->email . '" disabled="true"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $password_error . '" placeholder="Enter their password" input_type="password" value=""]') ?>

                <button type="submit" class="app-btn primary-btn" name="update-pm">Update</button>
            </div>
        </form>
    </div>
</div>
<?php get_footer() ?>