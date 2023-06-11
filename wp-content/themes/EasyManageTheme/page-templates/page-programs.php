<?php
if (!is_user_trainer() && !is_user_p_manager()) {
    wp_redirect(home_url());
}


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
        <div class="table-heading-top">
            <h4>Programs</h4>

            <div>
                <!-- <form action="" method="get">
                    <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                    ?>
                </form> -->
                <a href="<?php echo site_url('/programs/create-program'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Program</button></a>
            </div>
        </div>
        <div class="table-heading-bottom">
            <!-- <form action="" method="get">
                <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                ?>
            </form> -->
        </div>
    </div>


    <?php
    $programs = get_programs(get_current_user_id());
    ?>

    <div class="programs-list">
        <?php
        foreach ($programs as $program) {
        ?>
            <div class="program">
                <div class="program-top">
                    <img src="<?php echo $program->program_logo ?>" alt="logo">

                    <span class="list-actions">
                        <ion-icon name='ellipsis-horizontal'></ion-icon>

                        <div class="more-actions">
                            <a href="<?php echo site_url('/programs/update-program?id=') . $program->id ?>" class="color-info"><ion-icon name='power'></ion-icon>Update</a>
                            </form>
                            <section class="separator"></section>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $program->id ?>">
                                <button type="submit" name="delete-program" class="btn-text color-danger"><ion-icon name="trash-outline"></ion-icon>Delete</button>
                            </form>
                        </div>
                    </span>
                </div>
                <div>
                    <p class="program-title"><?php echo $program->program_name ?></p>
                    <p class="program-description"><?php echo $program->program_description ?></p>
                </div>
                <section class="separator"></section>
                <div class="program-more">
                    <div>
                        <div>Trainer:</div>
                        <?php
                        $user = get_single_employee($program->program_assigned_to);
                        echo $user->fullname ?? '--';
                        ?>
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