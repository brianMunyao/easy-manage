<?php

/**
 * 
 * Template Name: Create Program Page Template
 */
get_header() ?>

<div class="app-padding create-trainer-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/programs') ?>'>/ Programs</a>
        <span>/ Create Program</span>
    </div>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Create Program</h2>

                <?php echo do_shortcode('[input_con name="name" label="Program Name" error="" placeholder="E.g. Angular Training"]') ?>
                <?php echo do_shortcode('[input_con name="description" label="Program Description" error="" placeholder="Brief 10 word explanation about the program"]') ?>
                <?php echo do_shortcode('[input_con name="logo" label="Logo URL" error="" placeholder="E.g. Link to a good angular logo" input_type="url"]') ?>

                <button type="submit" class="app-btn primary-btn" name="create-program">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>