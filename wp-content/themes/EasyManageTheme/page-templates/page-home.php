<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

/**
 * 
 * Template Name: Homepage Template
 */
get_header() ?>

<div class="home-page app-padding">
    <div class="greeting-search">
        <div class="greeting-con">
            <p class="greeting">
                <?php
                $name = get_user_meta_custom(get_current_user_id());
                $greeting = get_greeting() . ", ";
                $greeting .=  $name != '' ? $name : get_userdata(get_current_user_id())->user_login;

                echo $greeting;
                ?>
            <p class="greeting-sub">Here is the latest in a nutshell</p>
        </div>

        <form action="" method="get">
            <?php echo do_shortcode('[search_bar]') ?>
        </form>
    </div>

    <?php
    if (is_user_admin_custom()) {
        $dash1_icon = 'people-outline';
        $dash1_val = count(get_users()) - 1;
        $dash1_label = "Total Employees";

        $dash2_icon = 'people';
        $dash2_val = count(get_users(['role' => 'program_manager']));
        $dash2_label = "Program Managers";

        $dash3_icon = 'people';
        $dash3_val = count(get_users(['role' => 'trainer']));
        $dash3_label = "Trainers";

        $dash4_icon = 'people-outline';
        $dash4_val = count(get_users(['role' => 'trainee']));
        $dash4_label = "Trainees";

        $projects = get_projects();
        $projects_ongoing = array_filter($projects, function ($project) {
            return $project->project_done == 0;
        });
        $projects_completed = array_filter($projects, function ($project) {
            return $project->project_done == 1;
        });
    } else {
        //TODO: for other users
    }



    ?>

    <div class="dash-cards">
        <?php echo do_shortcode('[dash_card icon="' . $dash1_icon . '" label="' . $dash1_label . '" value="' . $dash1_val . '"]') ?>
        <?php echo do_shortcode('[dash_card icon="' . $dash2_icon . '" label="' . $dash2_label . '" value="' . $dash2_val . '"]') ?>
        <?php echo do_shortcode('[dash_card icon="' . $dash3_icon . '" label="' . $dash3_label . '" value="' . $dash3_val . '"]') ?>
        <?php echo do_shortcode('[dash_card icon="' . $dash4_icon . '" label="' . $dash4_label . '" value="' . $dash4_val . '"]') ?>




    </div>

    <div class="dash-bottom">
        <div class="dash-recent">
            <?php

            /**
             * 
             * TODO: get recent accounts here
             */
            $recents = [
                ["name" => "Drew Barrymore", "role" => "Program Manager", "created_on" => 'Jul 6'],
                ["name" => "Drew Barrymore", "role" => "Program Manager", "created_on" => 'Jul 6'],
                ["name" => "Drew Barrymore", "role" => "Program Manager", "created_on" => 'Jul 6'],
            ];
            ?>
            <table>
                <tr class="table-h">
                    <th colspan="3">Recent Accounts</th>
                </tr>

                <tr class="table-h">
                    <th class="tr-flex">Name</th>
                    <th class="role">Role</th>
                    <th class="created-on">Created On</th>
                </tr>

                <?php
                foreach ($recents as $recent) {
                    $recent = (object)$recent;
                ?>
                    <tr class="table-c">
                        <td class="name tr-flex"><?php echo $recent->name ?></td>
                        <td class="role"><?php echo $recent->role ?></td>
                        <td class="created-on"><?php echo $recent->created_on ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>

        <div class="overview">
            <p class="overview-title">Projects Status</p>

            <div class="progress">
                <div style="width: <?php echo calculate_percentage($projects_completed, $projects); ?>"></div>
            </div>

            <div class="overview-details">
                <div class="overview-row overview-row-h">
                    <span class="title">Total</span>
                    <span class="value"><?php echo count($projects) ?></span>
                </div>
                <div class="overview-row">
                    <div>
                        <div class="dot bg-primary"></div>
                        <span class="title">Completed</span>
                    </div>
                    <span class="value"><?php echo count($projects) - count($projects_ongoing) ?></span>
                </div>
                <div class="overview-row">
                    <div>
                        <div class="dot bg-secondary"></div>
                        <span class="title">Ongoing</span>
                    </div>
                    <span class="value"><?php echo count($projects_ongoing) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer() ?>