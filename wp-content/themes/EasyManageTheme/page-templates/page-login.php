<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

global $form_error;
$form_error = '';
global $email_error;
$email_error = '';
global $pass_error;
$pass_error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $email_error = validate_email_custom($email);
    $pass_error = validate_password_custom($password);

    if (empty($email_error) && empty($pass_error)) {
        // TODO: Change this to api

        $user = wp_signon([
            'user_login' => $email,
            'user_password' => $password
        ]);

        if (is_wp_error($user)) {
            $form_error = $user->get_error_message();
        }
    }
}

?>


<?php

/**
 * 
 * Template Name: Login Page Template
 */
get_header() ?>



<div class="form-container">
    <form action="" method="post">
        <div class="form">
            <h2>Login</h2>

            <div class="error"><?php echo $form_error ?></div>

            <?php
            $curr_email = $_POST["email"] ?? "";
            $curr_password = $_POST["password"] ?? "";
            ?>

            <?php echo do_shortcode('[input_con name="email" label="Email Address" error="' . $email_error . '" placeholder="Enter your email address" input_type="email" value="' . $curr_email . '"]') ?>
            <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $pass_error . '" placeholder="Enter your email password" input_type="password" value="' . $curr_password . '"]') ?>

            <button type="submit" class="app-btn primary-btn" name="login">Login</button>
        </div>
    </form>
</div>

<?php get_footer() ?>