<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

if (!is_user_admin_custom()) {
    wp_redirect(home_url());
}

if (isset($_POST['deactivate-user'])) {
    $res = deactivate_employee($_POST['id']);
}
if (isset($_POST['activate-user'])) {
    $res = activate_employee($_POST['id']);
}
if (isset($_POST['delete-user'])) {
    $res = delete_employee($_POST['id']);
}
if (isset($_POST['restore-user'])) {
    $res = restore_employee($_POST['id']);
}

/**
 * 
 * Template Name: Employees Page Template
 */
get_header() ?>

<div class="app-padding employees-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Employees</span>
    </div>

    <div class="table-heading">
        <div class="table-heading-top">
            <h3>
                <?php
                $section_title = 'Employees';
                if (!isset($_GET['cat'])) {
                    $section_title = 'Employees';
                    echo 'All ' . $section_title;
                } else {
                    $section_title = ucwords(str_replace('_', ' ', $_GET['cat'])) . 's';
                    echo $section_title;
                }
                ?>
            </h3>

            <div>
                <form action="<?php echo site_url('/search') ?>" method="get">
                    <?php echo do_shortcode('[search_bar placeholder="Search Employees"]') ?>
                </form>
                <a href="<?php echo site_url('/employees/create-program-manager'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Program Manager</button></a>
            </div>
        </div>
        <div class="table-heading-bottom">
            <form action="<?php echo site_url('/search') ?>" method="get">
                <?php echo do_shortcode('[search_bar placeholder="Search Employees"]') ?>
            </form>
        </div>
    </div>
    <?php

    $employees = get_employees_new();

    if (isset($_GET['cat'])) {
        $employees = array_filter($employees, function ($employee) {
            return $employee->role == $_GET['cat'];
        });
    }

    $active_employees = array_filter($employees, function ($employee) {
        return $employee->is_deactivated == 0 && $employee->is_deleted == 0;
    });
    $inactive_employees = array_filter($employees, function ($employee) {
        return $employee->is_deactivated == 1 && $employee->is_deleted == 0;
    });
    $deleted_employees = array_filter($employees, function ($employee) {
        return $employee->is_deleted == 1;
    });

    ?>

    <div class="categories">
        <a href="<?php echo site_url('/employees') ?>" class="<?php echo isset($_GET['cat']) ? '' : 'color-blue' ?>">All </a> |
        <a href="<?php echo site_url('/employees?cat=program_manager') ?>" class="<?php echo isset($_GET['cat']) && $_GET['cat'] == 'program_manager' ? 'color-blue' : '' ?>">Program Managers</a> |
        <a href="<?php echo site_url('/employees?cat=trainer') ?>" class="<?php echo isset($_GET['cat']) && $_GET['cat'] == 'trainer' ? 'color-blue' : '' ?>">Trainers</a> |
        <a href="<?php echo site_url('/employees?cat=trainee') ?>" class="<?php echo isset($_GET['cat']) && $_GET['cat'] == 'trainee' ? 'color-blue' : '' ?>">Trainees </a>
    </div>

    <div class="table-h">
        <span class="color-success">Active <?php echo $section_title ?> (<?php echo count($active_employees) ?>)</span>
    </div>
    <table style="width:100%">
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:150px">Role</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($active_employees) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Active <?php echo $section_title ?></td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($active_employees as $employee) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $employee->fullname ?></td>
                    <td style="width:150px"><?php echo ucwords(str_replace('_', ' ', $employee->role)) ?></td>
                    <td style="width:80px;"><?php echo !$employee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <?php if ($employee->role == 'program_manager') { ?>
                            <a href="<?php echo site_url('/employees/update-program-manager?id=') . $employee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <?php } else { ?>
                            <span></span>
                        <?php } ?>

                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $employee->id ?>">
                                    <button type="submit" name="deactivate-user" class="btn-text color-info"><ion-icon name='power'></ion-icon>Deactivate</button>
                                </form>
                                <section class="separator"></section>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $employee->id ?>">
                                    <button type="submit" name="delete-user" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
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
        <span class="color-danger">Inactive <?php echo $section_title ?> (<?php echo count($inactive_employees) ?>)</span>
    </div>
    <table style="width:100%">
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:150px">Role </th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($inactive_employees) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Inactive <?php echo $section_title ?></td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($inactive_employees as $employee) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $employee->fullname ?></td>
                    <td style="width:150px"><?php echo ucwords(str_replace('_', ' ', $employee->role)) ?></td>
                    <td style="width:80px;"><?php echo !$employee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <?php if ($employee->role == 'program_manager') { ?>
                            <a href="<?php echo site_url('/employees/update-program-manager?id=') . $employee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <?php } else { ?>
                            <span></span>
                        <?php } ?>
                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $employee->id ?>">
                                    <button type="submit" name="activate-user" class="btn-text color-info"><ion-icon name='power'></ion-icon>Activate</button>
                                </form>
                                <section class="separator"></section>
                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $employee->id ?>">
                                    <button type="submit" name="delete-user" class="btn-text color-danger"><ion-icon name='trash'></ion-icon>Delete</button>
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
        <span class="color-danger">Deleted <?php echo $section_title ?> (<?php echo count($deleted_employees) ?>)</span>
    </div>
    <table style="width:100%">
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:150px">Role</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>

        <?php if (count($deleted_employees) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No Deleted <?php echo $section_title ?></td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($deleted_employees as $employee) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $employee->fullname ?></td>
                    <td style="width:150px"><?php echo ucwords(str_replace('_', ' ', $employee->role)) ?></td>
                    <td style="width:80px;"><?php echo !$employee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <?php if ($employee->role == 'program_manager') { ?>
                            <a href="<?php echo site_url('/employees/update-program-manager?id=') . $employee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                        <?php } else { ?>
                            <span></span>
                        <?php } ?>
                        <span class="list-actions">
                            <ion-icon name='ellipsis-horizontal'></ion-icon>

                            <div class="more-actions">

                                <form action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $employee->id ?>">
                                    <button type="submit" name="restore-user" class="btn-text color-blue"><ion-icon name="arrow-undo-circle-outline"></ion-icon>Restore</button>
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