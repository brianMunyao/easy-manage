<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (isset($_GET['q'])) {
    $q = $_GET['q'];
} else {
    $q = "";
}
$employees = search_employees($q);
/**
 * 
 * Template Name: Search Page Template
 */
get_header() ?>
<div class="app-padding search-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <span>/ Search</span>
    </div>

    <div class="table-heading">
        <div class="table-heading-top">
            <h3>
                Employees Search
            </h3>

            <div>
                <form action="<?php echo site_url('/search') ?>" method="get">
                    <?php echo do_shortcode('[search_bar placeholder="search" value="' . $q . '"]') ?>
                </form>
            </div>
        </div>
        <div class="table-heading-bottom">
            <form action="<?php echo site_url('/search') ?>" method="get">
                <?php echo do_shortcode('[search_bar placeholder="search" value="' . $q . '"]') ?>
            </form>
        </div>
    </div>


    <div class="table-h">
        <span><?php echo count($employees) ?> Result(s) found for "<?php echo $q ?>"</span>
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

        <?php if (count($employees) == 0) { ?>
            <tr class="table-c">
                <td class="empty-row" colspan="5">No results found</td>
            </tr>
            <?php } else {

            $i = 0;
            foreach ($employees as $employee) {
            ?>
                <tr class="table-c">
                    <td style="width: 30px"><?php echo ++$i; ?></td>
                    <td class="name tr-flex"><?php echo $employee->fullname ?></td>
                    <td style="width:150px"><?php echo ucwords(str_replace('_', ' ', $employee->role)) ?></td>
                    <td style="width:80px;"><?php echo !$employee->is_deactivated ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                    <td style="width:100px" class="actions">
                        <a href="<?php echo site_url('/employees/update-program-manager?id=') . $employee->id  ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
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

</div>


<?php get_footer() ?>