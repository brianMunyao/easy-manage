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
        <h4>Company Trainees</h4>

        <div>
            <form action="" method="get">
                <?php echo do_shortcode('[search_bar placeholder="Search"]') ?>
            </form>
            <a href="<?php echo site_url('/trainees/create-trainee'); ?>"><button class="app-btn secondary-btn"><ion-icon name='add'></ion-icon> Add Trainee</button></a>
        </div>
    </div>
    <?php

    /**
     * 
     * TODO: get recent accounts here
     */
    $employees = [
        ["name" => "Drew Barrymore", "stack" => "WordPress", "status" => 1],
        ["name" => "Drew Barrymore", "stack" => "Angular", "status" => 1],
    ];
    ?>
    <table style="width:100%">
        <!-- <tr class="table-h">
            <th colspan="5">Active Accounts</th>
        </tr> -->
        <tr class="table-h">
            <th style="width: 30px">No.</th>
            <th class="tr-flex">Name</th>
            <th style="width:150px">Stack</th>
            <th style="width:80px;">Status</th>
            <th style="width:100px">Actions</th>
        </tr>


        <?php
        $i = 0;
        foreach ($employees as $employee) {
            $employee = (object)$employee;
        ?>
            <tr class="table-c">
                <td style="width: 30px"><?php echo ++$i; ?></td>
                <td class="name tr-flex"><?php echo $employee->name ?></td>
                <td style="width:150px"><?php echo $employee->stack ?></td>
                <td style="width:80px;"><?php echo $employee->status ? "<span class='status-active'>Active</span>" : "<span class='status-inactive'>Inactive</span>" ?></td>
                <td style="width:100px" class="actions">
                    <a href="<?php echo site_url('/trainees/update-trainee?id=1') ?>"><ion-icon name='create' class="color-blue"></ion-icon></a>
                    <span class="list-actions">
                        <ion-icon name='ellipsis-horizontal'></ion-icon>

                        <div class="more-actions">
                            <div class="color-info"><ion-icon name='power'></ion-icon>Activate</div>
                            <section class="separator"></section>
                            <div class="color-danger"><ion-icon name='trash'></ion-icon>Delete</div>
                        </div>
                    </span>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<?php get_footer() ?>