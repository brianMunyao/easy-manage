<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php if (!is_user_trainer() && !is_user_trainee()) wp_redirect(home_url()) ?>

<?php

/**
 * 
 * Template Name: Projects Page Template
 */
get_header() ?>

<?php

$trainer_has_program = false;
$projects = [];

if (is_user_trainer()) {
    $assigned_cohort = get_program_assignee(get_current_user_id());
    if (!is_response_error($assigned_cohort)) {
        $trainer_has_program = true;
    }

    $projects = get_all_projects(get_current_user_id());
} else {
    $projects = get_trainees_projects(get_current_user_id());
}
$ongoing = array_filter($projects, function ($project) {
    return $project->project_done == 0;
});
$completed = array_filter($projects, function ($project) {
    return $project->project_done == 1;
});


?>



<div class="projects-page app-padding">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Projects</span>
    </div>

    <?php

    if (is_user_trainer() && !$trainer_has_program) {
    ?>
        <div style="width: 100%;height:70%;opacity:0.4;display:flex;align-items:center;justify-content:center;">
            <h3>Sorry, You have not been assigned a cohort</h3>
        </div>
    <?php
    } else {
    ?>

        <div class="table-heading">
            <div class="table-heading-top">
                <h4>Projects</h4>

                <div>
                    <!-- <form action="" method="get">
                    <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                    ?>
                </form> -->
                    <?php if (is_user_trainer()) { ?>
                        <a href="<?php echo site_url('/projects/create-project'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Project</button></a>
                    <?php } ?>
                </div>
            </div>
            <div class="table-heading-bottom">
                <!-- <form action="" method="get">
                <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                ?>
            </form> -->
            </div>
        </div>

        <div class="table-h">
            <span class="color-success">Active Projects (<?php echo count($ongoing) ?>)</span>
        </div>

        <?php if (count($ongoing) == 0) { ?>
            <div class="empty-list">No Active Projects</div>
        <?php } else { ?>
            <div class="projects-list">
                <?php
                foreach ($ongoing as $project) {
                    // $project_tasks = get_tasks($project->project_id);
                    $project_task = [];
                    $completed_tasks  = [];
                    // $completed_tasks = array_filter($project_tasks, function ($task) {
                    //     return $task->task_done == 1;
                    // });
                ?>
                    <a href="<?php echo site_url('/projects/project?id=') . $project->project_id ?>">
                        <div class="project">
                            <p class="project-title">
                                <?php echo $project->project_name ?>
                            </p>

                            <div class="project-category">
                                <?php echo $project->project_category ?>
                            </div>

                            <div class="project-progress-con">
                                <div class="project-progress">
                                    <div style="width: <?php echo '50%'; //calculate_percentage($completed_tasks, $project_tasks) 
                                                        ?>;"></div>
                                </div>


                                <div><?php echo '50%'; //calculate_percentage($completed_tasks, $project_tasks) 
                                        ?></div>
                            </div>

                            <div class="project-bottom-con">
                                <div class="separator"></div>

                                <div class="project-bottom">
                                    <span class="project-due">
                                        <ion-icon name='calendar-outline'></ion-icon>
                                        Due: <?php echo $project->project_due_date  ?>
                                    </span>

                                    <div class="project-assignees">
                                        <?php
                                        $assignees = explode(",", $project->project_assignees);
                                        foreach ($assignees as $assignee) {
                                        ?>
                                            <div class="user-icon">
                                                <?php
                                                $temp = get_user_by('id', (int)$assignee);
                                                $temp = get_user_meta($assignee, 'fullname', true);
                                                echo get_initials($temp);
                                                ?>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php
                }
                ?>
            </div>
        <?php } ?>


        <div class="table-h">
            <span class="color-error">Completed Projects (<?php echo count($completed) ?>)</span>
        </div>
        <?php if (count($completed) == 0) { ?>
            <div class="empty-list">No Completed Projects</div>
        <?php } else { ?>
            <div class="projects-list">
                <?php
                foreach ($completed as $project) {
                    $project_tasks = []; //TODO: work on this
                    // $project_tasks = get_tasks($project->project_id);

                    $completed_tasks = array_filter($project_tasks, function ($task) {
                        return $task->task_id == 1;
                    });
                ?>
                    <a href="<?php echo site_url('/projects/project?id=') . $project->project_id ?>">
                        <div class="project">
                            <p class="project-title">
                                <?php echo $project->project_name ?>
                            </p>

                            <div class="project-category">
                                <?php echo $project->project_category ?>
                            </div>

                            <div class="project-progress-con">
                                <div class="project-progress">
                                    <div style="width: <?php echo calculate_percentage($completed_tasks, $project_tasks) ?>;"></div>
                                </div>

                                <div><?php echo calculate_percentage($completed_tasks, $project_tasks) ?></div>
                            </div>

                            <div class="project-bottom-con">
                                <div class="separator"></div>

                                <div class="project-bottom">
                                    <span class="project-due">
                                        <ion-icon name='calendar-outline'></ion-icon>
                                        Due: <?php echo $project->project_due_date  ?>
                                    </span>

                                    <div class="project-assignees">
                                        <div class="user-icon">BK</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php
                }
                ?>
            </div>
        <?php } ?>

    <?php } ?>
</div>


<?php get_footer() ?>