<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $project = get_single_project_new($id);
    if (is_response_error($project)) wp_redirect('/projects');
    $project = $project->data;
} else {
    wp_redirect(site_url('/projects'));
}

$form_error = $form_success = '';

$project_name_error = $project_category_error = $project_description_error = $project_duedate_error = $project_assignees_error = '';


if (isset($_POST['update-project'])) {

    $project_name = $_POST['project_name'];
    $project_category = $_POST['project_category'];
    $project_description = $_POST['project_description'];
    $project_duedate = $_POST['project_duedate'];
    $project_assignees = isset($_POST['project_assignees']) ? $_POST['project_assignees'] : [];

    $project_name_error = validate_field_custom($project_name);
    $project_category_error = validate_field_custom($project_category);
    $project_description_error = validate_field_custom($project_description);
    $project_duedate_error = validate_field_custom($project_duedate);

    if (gettype($project_assignees) === 'array' && count($project_assignees) > 0) {
        $project_assignees_error = "";
    } else {
        $project_assignees_error = "Field is required";
    }

    if (empty($project_name_error) && empty($project_category_error) && empty($project_description_error) && empty($project_duedate_error) && empty($project_assignees_error)) {
        $project_assignees = array_values($project_assignees);

        $result = update_project([
            'project_id' => $project->project_id,
            'project_name' => $project_name,
            'project_category' => $project_category,
            'project_description' => $project_description,
            'project_due_date' => $project_duedate,
            'project_program_id' => $project->project_program_id,
            'project_assignees' => $project_assignees
        ]);

        if (is_response_error($result)) {
            $form_error = $result->message ?? "Update Failed";
        } else {
            $form_success = "Successfully Updated";
        }
    }
}

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
    $available_assignees = get_available_trainees($project->project_program_id);
    ?>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Project</h2>
                <p class="success"><?php echo $form_success ?></p>
                <p class="error"><?php echo $form_error ?></p>

                <?php
                $curr_project_name = $_POST['project_name'] ?? $project->project_name;
                $curr_project_category = $_POST['project_category'] ?? $project->project_category;
                $curr_project_description = $_POST['project_description'] ?? $project->project_description;
                $curr_project_duedate = $_POST['project_duedate'] ?? $project->project_due_date;

                $default_assignees = [];
                if ($project->project_assignees) {
                    foreach (explode(",", $project->project_assignees) as $assignee) {
                        $user = get_single_employees_new($assignee);
                        if (!is_response_error($user)) {
                            $user = $user->data;

                            array_push($default_assignees, $assignee);

                            if (!in_array($user->id, array_column($available_assignees, 'id'))) {
                                array_push($available_assignees, $user);
                            }
                        }
                    }
                }
                $curr_project_assignees = isset($_POST['project_assignees']) ? $_POST['project_assignees'] : $default_assignees;
                ?>

                <?php echo do_shortcode('[input_con name="project_name" label="Project Name" error="' . $project_name_error . '" placeholder="Enter the project name" value="' . $curr_project_name . '"]') ?>
                <?php echo do_shortcode('[input_con name="project_category" label="Project Category" error="' . $project_category_error . '" placeholder="E.g. Mobile App, Web App" value="' . $curr_project_category . '"]') ?>
                <?php echo do_shortcode('[input_con name="project_description" label="Project Description" error="' . $project_description_error . '" placeholder="Briefly explain the project expectations" input_type="textarea" value="' . $curr_project_description . '"]') ?>
                <?php echo do_shortcode('[input_con name="project_duedate" label="Project Due Date" error="' . $project_duedate_error . '" input_type="date" value="' . $curr_project_duedate . '"]') ?>

                <div class="input-con">
                    <label for="assignee-picker-drop">Pick Assignee(s)</label>

                    <div class="trainees-dropdown">
                        <?php
                        if (isset($curr_project_assignees)) {
                            $curr_project_assignees = array_values($curr_project_assignees);
                        } else {
                            $curr_project_assignees = [];
                        }
                        // var_dump($curr_project_assignees);
                        // var_dump($available_assignees);
                        foreach ($available_assignees as $assignee) {
                        ?>
                            <label for="<?php echo $assignee->id ?>" class="trainees-option">
                                <input type="checkbox" name="project_assignees[]" value="<?php echo $assignee->id ?>" id="<?php echo $assignee->id ?>" <?php echo in_array((string)$assignee->id, $curr_project_assignees) ? 'checked' : ''; ?>>

                                <span><?php echo $assignee->fullname ?></span>
                            </label>

                        <?php
                        }
                        ?>
                    </div>
                    <p class="form-error color-danger"><?php echo $project_assignees_error ?></p>
                </div>

                <button type="submit" class="app-btn primary-btn" name="update-project">Update</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>