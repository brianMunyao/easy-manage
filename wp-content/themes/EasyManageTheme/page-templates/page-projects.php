<?php

/**
 * 
 * Template Name: Projects Page Template
 */
get_header() ?>

<?php
$projects = get_projects();
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

    <div class="table-heading">
        <h3>Projects</h3>

        <div>
            <!-- <form action="" method="get">
                <?php // echo do_shortcode('[search_bar placeholder="Search"]') 
                ?>
            </form> -->
            <a href="<?php echo site_url('/projects/create-project'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Project</button></a>
        </div>
    </div>

    <div class="table-h">
        Active Projects (<?php echo count($ongoing) ?>)
    </div>

    <?php if (count($ongoing) == 0) { ?>
        <div class="empty-list">No Active Projects</div>
    <?php } else { ?>
        <div class="projects-list">
            <?php
            foreach ($ongoing as $project) {
                $project_tasks = get_tasks($project->project_id);

                $completed_tasks = array_filter($project_tasks, function ($task) {
                    return $task->task_done == 1;
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


    <div class="table-h">
        Completed Projects (<?php echo count($completed) ?>)
    </div>
    <?php if (count($completed) == 0) { ?>
        <div class="empty-list">No Completed Projects</div>
    <?php } else { ?>
        <div class="projects-list">
            <?php
            foreach ($completed as $project) {
                $project_tasks = get_tasks($project->project_id);

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

</div>

<?php get_footer() ?>