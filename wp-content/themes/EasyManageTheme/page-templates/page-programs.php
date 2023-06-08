<?php

/**
 * 
 * Template Name: Programs Page Template
 */
get_header() ?>

<div class="programs-page app-padding">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Programs</span>
    </div>

    <div class="table-heading">
        <h4>Programs</h4>

        <div>
            <form action="" method="get">
                <?php echo do_shortcode('[search_bar placeholder="Search"]') ?>
            </form>
            <a href="<?php echo site_url('/programs/create-program'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Program</button></a>
        </div>
    </div>


    <?php
    $programs = [
        ['name' => 'Angular Training', 'description' => 'Learn modern web development with TypeScript and Angular.', 'logo' => 'https://cdn4.iconfinder.com/data/icons/logos-and-brands/512/21_Angular_logo_logos-1024.png'],
        ['name' => 'WordPress Training', 'description' => 'Build dynamic websites with ease using WordPress platform.', 'logo' => 'https://cdn4.iconfinder.com/data/icons/logos-and-brands/512/382_Wordpress_logo-1024.png']
    ];
    ?>
    <div class="table-h">
        Active Programs (<?php echo count($programs) ?>)
    </div>
    <div class="programs-list">
        <?php
        foreach ($programs as $program) {
            $program = (object)$program;
        ?>
            <div class="program">
                <div>
                    <img src="<?php echo $program->logo ?>" alt="logo">
                    <p class="program-title"><?php echo $program->name ?></p>
                    <p class="program-description"><?php echo $program->description ?></p>
                </div>
                <section class="separator"></section>
                <div class="program-more">
                    <div>
                        <div>Trainer:</div> John D
                    </div>
                    <div>
                        <div>Trainees:</div> 8
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>





</div>

<?php get_footer() ?>