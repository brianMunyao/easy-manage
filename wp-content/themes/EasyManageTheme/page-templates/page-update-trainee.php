<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

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

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="" placeholder="Enter their fullname"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" error="" placeholder="Enter their email address" input_type="email"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="" placeholder="Enter their password" input_type="password"]') ?>

                <button type="submit" class="app-btn primary-btn" name="update-trainee">Update</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>