<?php

/**
 * 
 * Template Name: Update Trainer Page Template
 */
get_header() ?>

<div class="app-padding update-trainer-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/trainers') ?>'>/ Trainers</a>
        <span>/ Update Trainer</span>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Trainer</h2>

                <?php echo do_shortcode('[input_con name="fullname" label="Fullname" error="" placeholder="Enter their fullname"]') ?>
                <?php echo do_shortcode('[input_con name="email" label="Email Address" error="" placeholder="Enter their email address" input_type="email"]') ?>
                <?php echo do_shortcode('[input_con name="password" label="Password" error="" placeholder="Enter their email password" input_type="password"]') ?>

                <div class="input-con">
                    <div>
                        <label for="program">Training Program</label>
                        <select name="program" id="program">
                            <option value="" selected disabled hidden>Select a training</option>
                            <?php
                            foreach ($programs as $program) {
                                $program = (object)$program;
                            ?>
                                <option value="<?php echo $program->name ?>"><?php echo $program->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="form-error color-danger"></p>
                </div>

                <button type="submit" class="app-btn primary-btn" name="update-trainer">Update</button>
            </div>
        </form>
    </div>
</div>
<?php get_footer() ?>