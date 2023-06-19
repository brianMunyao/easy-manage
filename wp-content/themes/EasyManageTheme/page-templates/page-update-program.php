<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $program = get_single_program_new($id);
    if (!$program) {
        wp_redirect(site_url('/programs'));
    }
} else {
    wp_redirect(site_url('/programs'));
}

$program_name_error = '';
$description_error = '';
$logo_error = '';

$form_error = '';
$form_success = '';

if (isset($_POST['update-program'])) {
    $program_name = $_POST['program_name'];
    $description = $_POST['description'];
    $logo = $_POST['logo'];

    $program_name_error = empty($program_name) ? 'Name is required' : '';
    $description_error = empty($description) ? 'Description is required' : '';
    $logo_error = empty($logo) ? 'Logo is required' : '';

    if (empty($program_name_error) && empty($description_error) && empty($logo_error)) {
        $result = update_program_new([
            'program_id' => $program->program_id,
            'program_name' => $program_name,
            'program_description' => $description,
            'program_logo' => $logo
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
 * Template Name: Update Program Page Template
 */
get_header() ?>

<div class="app-padding update-trainer-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/programs') ?>'>/ Programs</a>
        <span>/ Update Program</span>
    </div>

    <div class="form-container">
        <form action="" method="post">
            <div class="form">
                <h2>Update Program</h2>

                <?php
                $curr_program_name = $_POST["program_name"] ?? $program->program_name;
                $curr_description = $_POST["description"] ?? $program->program_description;
                $curr_logo = $_POST["logo"] ?? $program->program_logo;
                ?>

                <p class="error"><?php echo $form_error ?></p>
                <p class="success"><?php echo $form_success ?></p>

                <?php echo do_shortcode('[input_con name="program_name" label="Program Name" error="' . $program_name_error . '" placeholder="E.g. Angular Training" value="' . $curr_program_name . '"]') ?>
                <?php echo do_shortcode('[input_con name="description" label="Program Description" error="' . $description_error . '" placeholder="Brief 10 word explanation about the program"  value="' . $curr_description . '"]') ?>
                <?php echo do_shortcode('[input_con name="logo" label="Logo URL" error="' . $logo_error . '" placeholder="E.g. Link to a good angular logo" input_type="url"  value="' . $curr_logo . '"]') ?>

                <button type="submit" class="app-btn primary-btn" name="update-program">Update</button>
            </div>
        </form>
    </div>
</div>
<?php get_footer() ?>