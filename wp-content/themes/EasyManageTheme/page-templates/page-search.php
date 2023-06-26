<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
$q = isset($_GET['q']) ? $_GET['q'] : "";

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
                <?php echo do_shortcode('[search_bar placeholder="Employee Search" value="' . $q . '"]') ?>
            </form>
        </div>
    </div>


    <div class="table-h">
        <span><?php echo count($employees) . " result(s) found for " . $q ?></span>
    </div>
    <table style="width:100%">
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th>Name</th>
            <th>Role</th>
            <th>Joined On</th>
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
                    <td style="width: 30px"><?php echo ++$i; ?>.</td>
                    <td>
                        <div class="icon-name-email">
                            <?php echo do_shortcode("[identicon str=" . $employee->email . "]") ?>
                            <div class="name-email">
                                <?php
                                if ($employee->fullname != $employee->email) {
                                ?>
                                    <p><?php echo $employee->fullname ?></p>
                                <?php
                                } else
                                ?>
                                <p><?php echo $employee->email ?></p>
                            </div>
                        </div>
                    </td>
                    <td><?php echo ucwords(str_replace('_', ' ', $employee->role)) ?></td>
                    <td><?php echo date('F jS, Y', strtotime($employee->registered_on)) ?></td>

                </tr>
        <?php
            }
        }
        ?>
    </table>
</div>

<?php get_footer() ?>