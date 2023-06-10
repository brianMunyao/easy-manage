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

            <div class="nav-links">
                <?php
                $current_url = get_permalink();
                if (is_user_admin_custom()) {
                    $menu_items = wp_get_nav_menu_items('admin-menu');

                    foreach ($menu_items as $menu_item) {
                        $title = $menu_item->title;
                        $url = $menu_item->url;
                        $is_active = false;

                        if ($current_url === $url) {
                            $is_active = true;
                        }


                        if (!$is_active && $current_url != home_url() . '/') {
                            $child_pages = get_pages(array('child_of' => $menu_item->object_id));
                            foreach ($child_pages as $child_page) {
                                if (get_permalink($child_page->ID) === $current_url) {
                                    $is_active = true;
                                    break;
                                }
                            }
                        }

                        echo '<a href="' . $url . '" class="nav-link ' . ($is_active ? "nav-link-active" : "") . '">' . $title . '</a>';
                    }
                }
                ?>
            </div>

            <div class="curr-user">
                Admin
            </div>
        </nav>

        <main>