<?php

/**
 * 
 * Template Name: Update Project Page Template
 */
get_header() ?>

<div class="app-padding update-project-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/projects') ?>'>/ Projects</a>
        <span>/ Update Project</span>
    </div>

    <?php

    $assignees = ['Drew Barrymore', 'Drew', 'Drew Barrymore', 'Drew Barrymore'];

    $available_assignees = [
        ["name" => 'Drew Barrymore'],
        ["name" => 'Barry'],
    ];

    ?>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Project</h2>

                <?php echo do_shortcode('[input_con name="name" label="Project Name" error="" placeholder="Enter the project name"]') ?>
                <?php echo do_shortcode('[input_con name="category" label="Project Category" error="" placeholder="E.g. Mobile App, Web App"]') ?>
                <?php echo do_shortcode('[input_con name="description" label="Project Description" error="" placeholder="Briefly explain the projcet expectations" input_type="textarea"]') ?>
                <?php echo do_shortcode('[input_con name="duedate" label="Project Due Date" error="" input_type="date"]') ?>

                <div class="input-con">
                    <label for="">Assignee(s)</label>
                    <div class="assignee-list">
                        <?php
                        if (count($assignees) > 0) {
                            foreach ($assignees as $assignee) {
                        ?>
                                <div class="assignee assigned"><?php echo $assignee ?> <span><ion-icon name='close'></ion-icon></span></div>
                            <?php
                            }
                        } else {
                            ?>
                            <div class="assignee unassigned">Unassigned</div>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="input-con">
                    <label for="assignee-picker">Pick Assignee</label>
                    <select name="assignee-picker" id="assignee-picker">
                        <option value="" selected disabled hidden style="color: red">Select An Assignee</option>
                        <?php
                        foreach ($available_assignees as $assignee) {
                            $assignee = (object)$assignee;
                        ?>
                            <option value="<?php echo $assignee->name ?>"><?php echo $assignee->name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="app-btn primary-btn" name="create-project">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>