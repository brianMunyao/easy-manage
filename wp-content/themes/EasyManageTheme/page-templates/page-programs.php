<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (!is_user_trainer() && !is_user_p_manager()) wp_redirect(home_url());


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
                <a href="<?php echo site_url('/programs/create-program'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Program</button></a>
            </div>
        </div>
        <div class="table-heading-bottom"></div>
    </div>


    <?php
    $programs = get_programs_new(get_current_user_id());

    if (count($programs) == 0) {
    ?>
        <p class="empty-list">No created programs</p>
    <?php
    }
    ?>

    <div class="programs-list">
        <?php
        foreach ($programs as $program) {
            $program_trainer = get_single_employees_new($program->program_assigned_to);
            $program_trainer = is_response_error($program_trainer) ? '' : $program_trainer->data;
        ?>
            <div class="program">
                <div class="program-top">
                    <img src="<?php echo $program->program_logo ?>" alt="logo">

                    <span class="list-actions">
                        <ion-icon name='ellipsis-horizontal'></ion-icon>

                        <div class="more-actions">
                            <a href="<?php echo site_url('/programs/update-program?id=') . $program->program_id ?>" class="color-info"><ion-icon name='power'></ion-icon>Update</a>
                            </form>
                            <section class="separator"></section>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $program->program_id ?>">
                                <button type="submit" name="delete-program" class="btn-text color-danger"><ion-icon name="trash-outline"></ion-icon>Delete</button>
                            </form>
                        </div>
                    </span>
                </div>
                <div style="flex:1">
                    <p class="program-title"><?php echo $program->program_name ?></p>
                    <p class="program-description"><?php echo $program->program_description ?></p>
                </div>
                <section class="separator"></section>
                <div class="program-more">
                    <div>
                        <div>Trainer:</div>
                        <?php echo $program_trainer->fullname ?? '--'; ?>
                    </div>
                    <div>
                        <div>Trainees:</div>
                        <?php
                        $trainees = get_trainees_in_program($program->program_id);
                        echo count($trainees);
                        ?>
                    </div>
                </div>

                <?php if (empty($program_trainer)) { ?>
                    <div class="circle-error" title="Program has no trainer">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                    </div>
                <?php } ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php get_footer() ?>