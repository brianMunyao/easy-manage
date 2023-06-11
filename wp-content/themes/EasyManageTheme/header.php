<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name') ?></title>
    <?php wp_head() ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-body">
        <nav class="app-padding">
            <span class="logo">
                <?php the_custom_logo() ?>
            </span>

            <?php if (is_user_logged_in()) { ?>
                <div class="nav-links">
                    <?php

                    if (is_user_admin_custom()) {
                        get_user_menu(wp_get_nav_menu_items('admin-menu'));
                    }
                    ?>
                </div>

                <form action="" method="post">
                    <span class="logged-user">
                        <?php
                        $email = wp_get_current_user()->user_email;
                        $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?d=identicon';
                        ?>
                        <img src="<?php echo $avatar ?>" alt="avatar">
                        <?php

                        $name = get_user_meta_custom(get_current_user_id());
                        echo $name != '' ? $name : get_userdata(get_current_user_id())->user_login;
                        ?>

                        <div>
                            <button class="app-btn danger-btn" name="logout" type="submit">Logout</button>
                        </div>
                    </span>
                </form>


                <span class="burger"><ion-icon name="menu"></ion-icon>
                    <div class="mob-nav-link">
                        <?php
                        if (is_user_admin_custom()) {
                            get_user_menu(wp_get_nav_menu_items('admin-menu'));
                        }
                        ?>

                        <span class="logged-user">
                            <?php
                            $email = wp_get_current_user()->user_email;
                            $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?d=identicon';
                            ?>
                            <img src="<?php echo $avatar ?>" alt="avatar">
                            <?php

                            $name = get_user_meta_custom(get_current_user_id());
                            echo $name != '' ? $name : get_userdata(get_current_user_id())->user_login;
                            ?>
                        </span>

                        <form action="" method="post">
                            <button class="app-btn danger-btn" name="logout" type="submit">Logout</button>
                        </form>

                    </div>
                </span>

            <?php } ?>

        </nav>

        <main>