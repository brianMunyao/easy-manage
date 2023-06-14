<?php
if (!is_user_p_manager()) {
    wp_redirect(home_url());
}

/**
 * 
 * Template Name: Trainers Page Template
 */
get_header() ?>
<div class="app-padding trainers-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Trainers</span>
    </div>

    <div class="table-heading">
        <div class="table-heading-top">
            <h4>Company Trainers</h4>

            <div>
                <form action="" method="get">
                    <?php echo do_shortcode('[search_bar placeholder="search"]') ?>
                </form>
                <a href="<?php echo site_url('/employees/create-trainer'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Trainer</button></a>
            </div>
        </div>
        <div class="table-heading-bottom">
            <form action="" method="get">
                <?php echo do_shortcode('[search_bar placeholder="search"]') ?>
            </form>
        </div>
    </div>

    <?php
    $trainers = get_trainers_new();

    $active_trainers = array_filter($trainers, function ($trainer) {
        return $trainer->is_deactivated == 0 && $trainer->is_deleted == 0;
    });
    $inactive_trainers = array_filter($trainers, function ($trainer) {
        return $trainer->is_deactivated == 1 && $trainer->is_deleted == 0;
    });
    $deleted_trainers = array_filter($trainers, function ($trainer) {
        return $trainer->is_deleted == 1;
    });
    ?>


    <div class="table-h">
        <span class="color-success">Active Trainers (<?php echo count($active_trainers) ?>)</span>
    </div>
    <table style="width:100%">
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th>Stack</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($active_trainers) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Active Trainers</td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($active_trainers as $trainer) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $trainer->fullname ?></td>
                    <td class=""><?php echo 'WordPress' //$trainer->stack 
                                    ?></td>
                    <td style="width:80px;"><?php echo !$trainer->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/trainers/update-trainer?id=') . $trainer->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>

                        <?php if (is_user_admin_custom()) { ?>
                            <span class="list-actions">
                                <ion-icon name='ellipsis-horizontal'></ion-icon>

                                <div class="more-actions">
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainer->id ?>">
                                        <button type="submit" name="deactivate-trainer" class="btn-text color-info"><ion-icon name='power'></ion-icon>Deactivate</button>
                                    </form>
                                    <section class="separator"></section>
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainer->id ?>">
                                        <button type="submit" name="delete-trainer" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                    </form>
                                </div>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </table>

    <div class="spacer"></div>

    <div class="table-h">
        <span class="color-danger">Inactive Trainers (<?php echo count($inactive_trainers) ?>)</span>
    </div>
    <table style="width:100%">
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th>Stack</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($inactive_trainers) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Inactive Trainers</td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($inactive_trainers as $trainer) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $trainer->fullname ?></td>
                    <td class=""><?php echo  'WordPress' //$trainer->stack 
                                    ?></td>
                    <td style="width:80px;"><?php echo !$trainer->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/trainers/update-trainer?id=') . $trainer->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>

                        <?php if (is_user_admin_custom()) { ?>
                            <span class="list-actions">
                                <ion-icon name='ellipsis-horizontal'></ion-icon>

                                <div class="more-actions">
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainer->id ?>">
                                        <button type="submit" name="activate-trainer" class="btn-text color-info"><ion-icon name='power'></ion-icon>Activate</button>
                                    </form>
                                    <section class="separator"></section>
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $trainer->id ?>">
                                        <button type="submit" name="delete-trainer" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
                                    </form>
                                </div>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </table>



</div>
<?php get_footer() ?>