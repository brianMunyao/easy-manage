<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php if (!is_user_trainer()) wp_redirect(home_url()) ?>

<?php

if (isset($_POST['deactivate-trainee'])) {
    $res = deactivate_employee($_POST['id']);
}
if (isset($_POST['activate-trainee'])) {
    $res = activate_employee($_POST['id']);
}

?>

<?php

/**
 * 
 * Template Name: Trainees Page Template
 */
get_header() ?>


<?php


$trainer_has_program = false;
$assigned_program = get_program_assignee(get_current_user_id());
if (!is_response_error($assigned_program)) {
    $trainer_has_program = true;
}
?>


<div class="app-padding trainees-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Trainees</span>
    </div>

    <?php

    if (!$trainer_has_program) {
    ?>
        <div style="width: 100%;height:70%;opacity:0.4;display:flex;align-items:center;justify-content:center;">
            <h3>Sorry, You have not been assigned a cohort</h3>
        </div>
    <?php
    } else {
    ?>

        <div class="table-heading">
            <div class="table-heading-top">
                <h4>Trainees</h4>

                <div>
                    <!-- <form action="" method="get">
                    <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                    ?>
                </form> -->
                    <a href="<?php echo site_url('/trainees/create-trainee'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Trainee</button></a>
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
        $trainees = get_trainees_in_program($assigned_program->program_id);

        $active_trainees = array_filter($trainees, function ($trainee) {
            return $trainee->is_deactivated == 0 && $trainee->is_deleted == 0;
        });
        $inactive_trainees = array_filter($trainees, function ($trainee) {
            return $trainee->is_deactivated == 1 && $trainee->is_deleted == 0;
        });
        ?>


        <div class="table-h">
            <span class="color-success">Active Trainees (<?php echo count($active_trainees) ?>)</span>
        </div>
        <table style="width:100%">
            <tr class="table-h">
                <th style="width: 30px">No.</th>
                <th>Name</th>
                <th>Status</th>
                <th style="width:100px">Actions</th>
            </tr>

            <?php if (count($active_trainees) == 0) { ?>
                <tr class="table-c">
                    <td class="empty-row" colspan="5">No Active Trainees</td>
                </tr>
                <?php } else {

                $i = 0;
                foreach ($active_trainees as $trainee) {
                ?>
                    <tr class="table-c">
                        <td style="width: 30px"><?php echo ++$i; ?></td>
                        <td class="name tr-flex"><?php echo $trainee->fullname ?></td>
                        <td><?php echo !$trainee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                        <td style="width:100px" class="actions">
                            <a href="<?php echo site_url('/trainees/update-trainee?id=') . $trainee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                            <span class="list-actions">
                                <ion-icon name='ellipsis-horizontal'></ion-icon>

                                <div class="more-actions">
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                        <button type="submit" name="deactivate-trainee" class="btn-text color-info"><ion-icon name='power'></ion-icon>Deactivate</button>
                                    </form>
                                    <?php
                                    if (is_user_admin_custom()) {
                                    ?>
                                        <section class="separator"></section>
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                            <button type="submit" name="delete-trainee" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                        </form>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </span>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </table>

        <div class="spacer"></div>

        <div class="table-h">
            <span class="color-danger">Inactive Trainees (<?php echo count($inactive_trainees) ?>)</span>
        </div>
        <table style="width:100%">

            <tr class="table-h">
                <th style="width: 30px">No.</th>
                <th>Name</th>
                <th>Status</th>
                <th style="width:100px">Actions</th>
            </tr>

            <?php if (count($inactive_trainees) == 0) { ?>
                <tr class="table-c">
                    <td class="empty-row" colspan="5">No Inactive Trainees</td>
                </tr>
                <?php } else {

                $i = 0;
                foreach ($inactive_trainees as $trainee) {
                ?>
                    <tr class="table-c">
                        <td style="width: 30px"><?php echo ++$i; ?></td>
                        <td class="name tr-flex"><?php echo $trainee->fullname ?></td>
                        <td><?php echo !$trainee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                        <td style="width:100px" class="actions">
                            <a href="<?php echo site_url('/trainees/update-trainee?id=') . $trainee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                            <span class="list-actions">
                                <ion-icon name='ellipsis-horizontal'></ion-icon>

                                <div class="more-actions">
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                        <button type="submit" name="activate-trainee" class="btn-text color-info"><ion-icon name='power'></ion-icon>Activate</button>
                                    </form>
                                    <?php

                                    if (is_user_admin_custom()) {
                                    ?>
                                        <section class="separator"></section>
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                            <button type="submit" name="delete-trainee" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                        </form>
                                    <?php
                                    }

                                    ?>
                                </div>
                            </span>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </table>
    <?php } ?>
</div>
<?php get_footer() ?>