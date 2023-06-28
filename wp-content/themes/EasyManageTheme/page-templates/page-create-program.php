<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

$program_name_error = $description_error = $logo_error = $start_date_error = $end_date_error = '';

$form_error = $form_success = '';

if (isset($_POST['create-program'])) {
    $program_name = $_POST['program_name'];
    $description = $_POST['description'];
    $logo = $_POST['logo'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $program_name_error = empty($program_name) ? 'Name is required' : '';
    $description_error = empty($description) ? 'Description is required' : '';
    $logo_error = empty($logo) ? 'Logo is required' : '';
    $start_date_error = empty($start_date) ? 'Field required' : '';
    $end_date_error = empty($end_date) ? 'Field required' : '';

    if (empty($program_name_error) && empty($description_error) && empty($logo_error) && empty($start_date_error) && empty($end_date_error)) {
        $result  = create_program_new([
            'program_name' =>  $program_name,
            'program_description' =>  $description,
            'program_logo' =>  $logo,
            'program_start_date' =>  $start_date,
            'program_end_date' =>  $end_date,
            // 'program_assigned_to'=>$request['program_assigned_to'],
            'program_created_by' =>   get_current_user_id(),
        ]);

        if (is_response_error($result)) {
            $form_error = $result->message ?? "Creation Failed";
        } else {
            $form_success = $result->message ?? "Successfully created";
        }
    }
}

/**
 * 
 * Template Name: Create Program Page Template
 */
get_header() ?>

<div class="app-padding create-program-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/programs') ?>'>/ Programs</a>
        <span>/ Create Program</span>
    </div>


    <div class="form-container">
        <form action="" method="POST">
            <div class="form">
                <h2>Create Program</h2>

                <?php
                $curr_program_name = $_POST["program_name"] ?? "";
                $curr_description = $_POST["description"] ?? "";
                $curr_logo = $_POST["logo"] ?? "";
                $curr_start_date = $_POST["start_date"] ?? "";
                $curr_end_date = $_POST["end_date"] ?? "";
                ?>


                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>

                <?php echo do_shortcode('[input_con name="program_name" label="Program Name" error="' . $program_name_error . '" placeholder="E.g. Angular or React.js" value="' . $curr_program_name . '"]') ?>
                <?php echo do_shortcode('[input_con name="description" label="Program Description" error="' . $description_error . '" placeholder="Brief 10 word explanation about the program"  value="' . $curr_description . '"]') ?>
                <?php echo do_shortcode('[input_con name="logo" label="Logo URL" error="' . $logo_error . '" placeholder="E.g. Link to a good angular logo" input_type="url"  value="' . $curr_logo . '"]') ?>

                <div class="start-end-date">
                    <?php echo do_shortcode('[input_con name="start_date" label="Start Date" error="' . $start_date_error . '" input_type="date"  value="' . $curr_start_date . '"]') ?>
                    <?php echo do_shortcode('[input_con name="end_date" label="End Date" error="' . $end_date_error . '" input_type="date"  value="' . $curr_end_date . '"]') ?>
                </div>

                <button type="submit" class="app-btn primary-btn" name="create-program">Create</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer() ?>