<?php

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

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="" placeholder="Enter their fullname"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" error="" placeholder="Enter their email address" input_type="email"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="" placeholder="Enter their email password" input_type="password"]') ?>

                <button type="submit" class="app-btn primary-btn" name="login">Login</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>