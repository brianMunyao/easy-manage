<?php if (is_user_logged_in()) {
    wp_redirect(home_url());
} ?>

<?php

$form_error = $form_success = $email_error = $pass_error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $email_error = validate_email_custom($email);
    $pass_error = validate_password_custom($password);

    if (empty($email_error) && empty($pass_error)) {
        $is_token = get_token($email, $password);

        if (is_response_error($is_token)) {
            $form_error = $is_token->message;
        } else {
            if (property_exists($is_token, 'data')) {
                $token_set = add_token_cookie($is_token->data);
                if ($token_set) {
                    $user = wp_signon([
                        'user_login' => $email,
                        'user_password' => $password
                    ]);
                } else {
                    wp_logout();
                }
            } else {
                $form_error = "Error getting token. Try Again Later.";
            }
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
            <div class="success"><?php echo $form_success ?></div>

            <?php
            $curr_email = $_POST["email"] ?? "";
            $curr_password = $_POST["password"] ?? "";
            ?>

            <?php echo do_shortcode('[input_con name="email" label="Email Address" error="' . $email_error . '" placeholder="Enter your email address" input_type="email" value="' . $curr_email . '"]') ?>
            <?php echo do_shortcode('[input_con name="password" label="Password" error="' . $pass_error . '" placeholder="Enter your password" input_type="password" value="' . $curr_password . '"]') ?>

            <button type="submit" class="app-btn primary-btn" name="login">Login</button>
        </div>
    </form>
</div>

<?php get_footer() ?>