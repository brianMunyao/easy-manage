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

<div class="login-page">
    <form action="" method="post">
        <div class="form">
            <h2>Login</h2>

            <div class="input-con">
                <div>
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder="Enter your email address">
                </div>
                <p class="form-error color-danger"><?php //error here
                                                    ?></p>
            </div>

            <div class="input-con">
                <div>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password">
                </div>
                <p class="form-error color-danger"><?php //error here
                                                    ?></p>
            </div>

            <button type="submit" class="app-btn primary-btn" name="login">Login</button>
        </div>
    </form>
</div>

<?php get_footer() ?>