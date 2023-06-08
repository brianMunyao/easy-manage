<?php

/**
 * 
 * Template Name: Homepage Template
 */
get_header() ?>

<div class="home-page app-padding">
    <div class="greeting-search">
        <div class="greeting-con">
            <p class="greeting">Good Morning Brian,</p>
            <p class="greeting-sub">Here is the latest in a nutshell</p>
        </div>

        <form action="" method="get">
            <?php echo do_shortcode('[search_bar]') ?>
        </form>
    </div>


    <div class="dash-cards">
        <div class="dash-card">
            <ion-icon name="business"></ion-icon>

            <p class="dash-number">19</p>
            <p class="dash-label">Total Employees</p>
        </div>
        <div class="dash-card">
            <ion-icon name="business"></ion-icon>

            <p class="dash-number">19</p>
            <p class="dash-label">Total Employees</p>
        </div>
        <div class="dash-card">
            <ion-icon name="business"></ion-icon>

            <p class="dash-number">19</p>
            <p class="dash-label">Total Employees</p>
        </div>
        <div class="dash-card">
            <ion-icon name="business"></ion-icon>

            <p class="dash-number">19</p>
            <p class="dash-label">Total Employees</p>
        </div>
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
                <div style="width: 30%"></div>
            </div>

            <div class="overview-details">
                <div class="overview-row overview-row-h">
                    <span class="title">Total</span>
                    <span class="value">21</span>
                </div>
                <div class="overview-row">
                    <div>
                        <div class="dot bg-primary"></div>
                        <span class="title">Completed</span>
                    </div>
                    <span class="value">12</span>
                </div>
                <div class="overview-row">
                    <div>
                        <div class="dot bg-secondary"></div>
                        <span class="title">Ongoing</span>
                    </div>
                    <span class="value">10</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer() ?>