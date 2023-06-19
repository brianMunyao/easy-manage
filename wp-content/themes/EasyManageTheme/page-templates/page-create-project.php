<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

$project_name_error = '';
$project_category_error = '';
$project_description_error = '';
$project_duedate_error = '';
$project_assignees_error = '';

if (isset($_POST['create-project'])) {
    $project_name = $_POST['project_name'];
    $project_category = $_POST['project_category'];
    $project_description = $_POST['project_description'];
    $project_duedate = $_POST['project_duedate'];
    $project_assignees = isset($_POST['project_assignees']) ? $_POST['project_assignees'] : "";

    $project_name_error = validate_field_custom($project_name);
    $project_category_error = validate_field_custom($project_category);
    $project_description_error = validate_field_custom($project_description);
    $project_duedate_error = validate_field_custom($project_duedate);
    $project_assignees_error = validate_field_custom(json_encode($project_assignees));


    if (empty($project_name_error) && empty($project_category_error) && empty($project_description_error) && empty($project_duedate_error) && empty($project_assignees_error)) {
        $project_assignees = array_values($project_assignees);

        echo "
    <pre>
    $project_name,
    $project_category,
    $project_description,
    $project_duedate ,
    " . json_encode($project_assignees) . ",
    </pre>
    ";
    }
}


/**
 * 
 * Template Name: Create Project Page Template
 */
get_header() ?>

<div class="app-padding create-project-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/projects') ?>'>/ Projects</a>
        <span>/ Create Project</span>
    </div>

    <?php


    $assignees = [];

    $available_assignees = [
        ['id' => 1, 'name' => 'brian'],
        ['id' => 2, 'name' => 'brian 2'],
        ['id' => 3, 'name' => 'brian 3']
    ];




    ?>


    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Create Project</h2>

                <?php
                $curr_project_name = $_POST['project_name'] ?? "";
                $curr_project_category = $_POST['project_category'] ?? "";
                $curr_project_description = $_POST['project_description'] ?? "";
                $curr_project_duedate = $_POST['project_duedate'] ?? "";
                $curr_project_assignees = isset($_POST['project_assignees']) ? $_POST['project_assignees'] : [];
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
                        foreach ($available_assignees as $assignee) {
                            $assignee = (object)$assignee;
                        ?>
                            <label for="<?php echo $assignee->id ?>" class="trainees-option">
                                <input type="checkbox" name="project_assignees[]" value="<?php echo $assignee->id ?>" id="<?php echo $assignee->id ?>" <?php echo in_array((string)$assignee->id, $curr_project_assignees) ? 'checked' : ''; ?>>

                                <span><?php echo $assignee->name ?></span>
                            </label>

                        <?php
                        }
                        ?>
                    </div>
                    <p class="form-error"><?php echo $project_assignees_error ?></p>
                </div>


                <!--  -->

                <!-- <input type="text" name="assignees" id='assignees' value=""> -->

                <!-- <div class="input-con">
                    <label for="assignee-picker">Pick Assignee</label>
                    <select name="assignee-picker" id="assignee-picker" multiple>
                        <option value="" selected disabled hidden style="color: red">Select An Assignee</option>

                        <option value="1">1,</option>
                        <option value="2df">2bnm,</option>
                        <option value="3df">3bnm,</option>
                    </select>
                    <p class="error select-error"><?php //echo $select_error 
                                                    ?></p>
                </div> -->



                <button type="submit" class="app-btn primary-btn" name="create-project">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>

<?php

echo "<script>
    let available_trainees = " . json_encode($available_assignees) . ";
    </script>";

?>
<!-- <script>
    let select = document.getElementById('assignee-picker');
    const assigneesInput = document.getElementById('assignees');


    const removeAllOptions = () => {
        let options = select.options;
        console.log(options)

        Array.from(options).forEach((opt, i) => {
            if (!opt.disabled) select.remove(i)
        })
    }

    const addAllOptions = (list) => {
        console.log('addAllOptions', list)
        list.forEach(assign => {
            let option = document.createElement('option');

            // option.id = assign.name
            option.value = assign.id;
            option.text = assign.name;
            option.disabled = assign.disabled
            select.appendChild(option);
        });
    }
    removeAllOptions();
    addAllOptions(available_trainees);

    select.addEventListener('change', (e) => {
        let selectedOption = select.value;
        let temp_assignees = assigneesInput.value
        if (!String(temp_assignees).split(',').includes(selectedOption)) {
            temp_assignees += selectedOption + ',';

            available_trainees = available_trainees.map((trainee, i) => {
                return {
                    ...trainee,
                    disabled: String(trainee.id) === selectedOption
                }
            });
            // available_trainees = available_trainees.filter(t => String(t.id) !== selectedOption);
            // console.log(available_trainees)
            removeAllOptions();
            addAllOptions(available_trainees);
            assigneesInput.value = temp_assignees;
        } else {
            document.querySelector('.select-error').innerHTML = 'User already inserted';

        }
    })
</script> -->