<?php

/**
 * 
 * Template Name: Login Page Template
 */
get_header() ?>

<?php

if (isset($_POST['login'])) {
    /**
     * 
     * TODO: handle submit here
     */
}

?>

<div class="form-container">
    <form action="" method="post">
        <div class="form">
            <h2>Login</h2>

            <?php echo do_shortcode('[input_con name="email" label="Email Address" error="" placeholder="Enter your email address" input_type="email"]') ?>
            <?php echo do_shortcode('[input_con name="password" label="Password" error="" placeholder="Enter your email password" input_type="password"]') ?>

            <button type="submit" class="app-btn primary-btn" name="login">Login</button>
        </div>
    </form>
</div>

<?php get_footer() ?>