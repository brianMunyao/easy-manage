<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

/**
 * 
 * Template Name: Trainees Page Template
 */
get_header() ?>
<div class="app-padding trainees-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Trainees</span>
    </div>

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
    $trainees = get_trainees(get_current_user_id());

    $active_trainees = array_filter($trainees, function ($trainee) {
        return $trainee->is_deactivated == 0 && $trainee->is_deleted == 0;
    });
    $inactive_trainees = array_filter($trainees, function ($trainee) {
        return $trainee->is_deactivated == 1 && $trainee->is_deleted == 0;
    });
    $deleted_trainees = array_filter($trainees, function ($trainee) {
        return $trainee->is_deleted == 1;
    });
    ?>


    <div class="table-h">
        <span class="color-success">Active Trainees (<?php echo count($active_trainees) ?>)</span>
    </div>
    <table style="width:100%">
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:80px;">Status</th>
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
                    <td style="width:80px;"><?php echo !$trainee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/trainees/update-trainee?id=') . $trainee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="deactivate-trainee" class="btn-text color-info"><ion-icon name='power'></ion-icon>Deactivate</button>
                                </form>
                                <section class="separator"></section>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="delete-trainee" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                </form>
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
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:80px;">Status</th>
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
                    <td style="width:80px;"><?php echo !$trainee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/trainees/update-trainee?id=') . $trainee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="activate-trainee" class="btn-text color-info"><ion-icon name='power'></ion-icon>Activate</button>
                                </form>
                                <section class="separator"></section>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="delete-trainee" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                </form>
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
        <span class="color-danger">Deleted Trainees (<?php echo count($deleted_trainees) ?>)</span>
    </div>
    <table style="width:100%">
        <!-- <tr class="table-h">
        <th colspan="5">Active Accounts</th>
    </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($deleted_trainees) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Deleted Trainees</td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($deleted_trainees as $trainee) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $trainee->fullname ?></td>
                    <td style="width:80px;"><?php echo !$trainee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/trainees/update-trainee?id=') . $trainee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="activate-trainee" class="btn-text color-info"><ion-icon name='power'></ion-icon>Activate</button>
                                </form>
                                <section class="separator"></section>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $trainee->id ?>">
                                    <button type="submit" name="restore-trainee" class="btn-text color-blue"><ion-icon name="arrow-undo-circle-outline"></ion-icon>Restore</button>
                                </form>
                            </div>
                        </span>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </table>


</div>
<?php get_footer() ?>