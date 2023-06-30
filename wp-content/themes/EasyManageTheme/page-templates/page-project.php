<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $form_error = '';
    $form_success = '';


    $project = get_single_project_new($id);
    if (is_response_error($project)) wp_redirect('/projects');
    $project = $project->data;

    $tasks = get_tasks($id);

    $ongoing = array_filter($tasks, function ($task) {
        return $task->task_done == 0;
    });
    $completed = array_filter($tasks, function ($task) {
        return $task->task_done == 1;
    });


    if (isset($_GET['task_id'])) {
        $opened_task = get_single_task($_GET['task_id']);
        $opened_task = $opened_task->data;
    }
} else {
    wp_redirect(site_url('/projects'));
}

if (isset($_POST['complete-project'])) {
    $res = complete_project($id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Update Failed";
    } else {
        $form_success = $res->message ?? "Successfully Updated";
        $project = get_single_project_new($id);
        if (is_response_error($project)) wp_redirect('/projects');
        $project = $project->data;
    }
}
if (isset($_POST['delete-project'])) {
    $res = delete_project($id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Deletion Failed";
    } else {
        $form_success =  $res->message ?? "Successfully Deleted";
    }

    do_action('move_to_projects');
}

if (isset($_POST['add-task'])) {
    $task = $_POST['task-name'];

    $res = create_task([
        'task_name' => $task,
        'task_project_id' => $id,
        'task_created_by' => get_current_user_id()
    ]);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Creation Failed";
    } else {
        $form_success =  $res->message ?? "Successfully Created";

        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
    }
}

if (isset($_POST['update-task'])) {
    $task = $_POST['task-name'];

    $res = update_task([
        'task_name' => $task,
    ], $_POST['task-id']);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Update Failed";
    } else {
        $form_success =  $res->message ?? "Successfully Updated";
        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
    }
}



if (isset($_POST['check-task'])) {
    $task_id = $_POST['task-id'];

    $res = complete_task($task_id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Update Failed";
    } else {
        $form_success = $res->message ?? "Successfully Updated";
        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
    }
}

if (isset($_POST['uncheck-task'])) {
    $task_id = $_POST['task-id'];

    $res = uncomplete_task($task_id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Update Failed";
    } else {
        $form_success = $res->message ?? "Successfully Updated";

        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
    }
}
if (isset($_POST['delete-task'])) {
    $task_id = $_POST['task-id'];

    $res = delete_task($task_id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Delete Failed";
    } else {
        $form_success = $res->message ?? "Successfully Deleted";
        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
    }
}

if (isset($_POST['give-remark'])) {
    $project_id = $_POST['project-id'];
    $remark = $_POST['remark'];

    if (!empty(trim($remark))) {
        $res = add_remark($project_id, [
            'remark_desc' => $remark,
            'remark_created_by' => get_current_user_id(),
            'remark_project_id' => $project_id
        ]);
    }
}



/**
 * 
 * Template Name: Single Project Page Template
 */
get_header() ?>


<script>
    const setUpdateID = (id) => {
        var params = new URLSearchParams(window.location.search);
        params.set('task_id', id);

        window.localStorage.setItem('updateTaskOpened', id)
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    const unsetUpdateID = () => {
        var params = new URLSearchParams(window.location.search);
        params.delete('task_id');

        window.location.href = window.location.pathname + '?' + params.toString();
    }

    const stopBodyScroll = () => document.querySelector('body').style.overflowY = 'hidden';
    const restoreBodyScroll = () => document.querySelector('body').style.overflowY = 'auto';

    const closeUpdateTaskModal = () => {
        window.localStorage.setItem('updateTaskOpened', null);
        unsetUpdateID()
    };


    window.onload = () => {
        document.querySelector('.update-task-modal').style.display = JSON.parse(localStorage.getItem('updateTaskOpened')) ? 'flex' : 'none';

        const modalInners = document.querySelectorAll('.modal-inner')
        modalInners.forEach((modalInner) => {
            modalInner.addEventListener('click', (e) => e.stopPropagation())
        })

        try {
            const addTaskBtn = document.querySelector('.add-task-btn');
            const addTaskModal = document.querySelector('.add-task-modal');
            const addTaskClose = document.querySelector('.add-task-close');

            addTaskBtn.addEventListener('click', () => {
                addTaskModal.style.display = 'flex';
                stopBodyScroll();
            })
            addTaskClose.addEventListener('click', () => {
                addTaskModal.style.display = 'none';
                restoreBodyScroll();
            })
            addTaskModal.addEventListener('click', () => {
                addTaskModal.style.display = 'none';
                restoreBodyScroll();
            })
        } catch (e) {
            console.log(e)
        }

        try {
            const updateTaskBtn = document.querySelector('.update-task-btn');
            const updateTaskModal = document.querySelector('.update-task-modal');
            const updateTaskClose = document.querySelector('.update-task-close');

            updateTaskBtn.addEventListener('click', () => {
                updateTaskModal.style.display = 'flex';
                stopBodyScroll();
                openUpdateTaskModal();
            })
            updateTaskClose.addEventListener('click', () => {
                updateTaskModal.style.display = 'none';
                restoreBodyScroll();
                closeUpdateTaskModal();
            })
            updateTaskModal.addEventListener('click', () => {
                updateTaskModal.style.display = 'none';
                restoreBodyScroll();
                closeUpdateTaskModal();
            })
        } catch (e) {
            console.log(e)
        }

        try {
            const giveRemarkBtn = document.querySelector('.give-remark-btn');
            const giveRemarkModal = document.querySelector('.give-remark-modal');
            const giveRemarkClose = document.querySelector('.give-remark-close');

            giveRemarkBtn.addEventListener('click', () => {
                giveRemarkModal.style.display = 'flex';
                stopBodyScroll();
            })
            giveRemarkClose.addEventListener('click', () => {
                giveRemarkModal.style.display = 'none';
                restoreBodyScroll();
            })
            giveRemarkModal.addEventListener('click', () => {
                giveRemarkModal.style.display = 'none';
                restoreBodyScroll();
            })
        } catch (e) {
            console.log(e)
        }
    }
</script>



<div class="app-padding project-page">
    <div class="nav-pages-links">
        <ion-icon name='home-outline'></ion-icon>
        <a href='<?php echo home_url() ?>'>Home</a>
        <a href='<?php echo site_url('/projects') ?>'>/ Projects</a>
        <span>/ <?php echo $project->project_name ?></span>
    </div>

    <div class="s-project-info">
        <div class="s-project-title"><?php echo $project->project_name ?></div>
        <div class="s-project-details">
            <span>Assignees:</span>
            <div>
                <?php
                $assignees = $project->project_assignees ? explode(",", $project->project_assignees) : [];
                echo count($assignees) == 0 ? '<span class="color-danger">Not assigned</span>' : '';
                foreach ($assignees as $assignee) {
                ?>
                    <div class="user-icon-more">
                        <?php
                        $temp = get_user_by('id', (int)$assignee);
                        $temp_name = get_user_meta($assignee, 'fullname', true);

                        $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($temp->user_email))) . '?d=identicon';
                        ?>
                        <img src="<?php echo $avatar ?>" alt="avatar">
                        <?php echo $temp_name; ?>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="s-project-details">
            <span>Deadline:</span>
            <div>
                <?php echo format_date($project->project_due_date) ?>
            </div>
        </div>
        <div class="s-project-details">
            <span>Category:</span>
            <div>
                <?php echo $project->project_category ?>
            </div>
        </div>
        <div class="s-project-details">
            <span>Description:</span>
            <div>
                <?php echo $project->project_description ?>
            </div>
        </div>

        <?php if ((is_user_trainer()) || (is_user_trainee() && $project->project_done == 0)) { ?>
            <form action="" method="post">
                <div class="s-project-details">
                    <span>Actions:</span>
                    <div class="s-links">
                        <?php
                        if (is_user_trainee()) {
                        ?>
                            <form action="" method="post">
                                <button type="submit" name="complete-project" class="btn-text color-success icon-text-link"><ion-icon name="paper-plane-outline"></ion-icon>Submit For Review</button>
                            </form>
                        <?php
                        }
                        ?>

                        <?php if (is_user_trainer()) { ?>
                            <a href="<?php echo site_url('/projects/update-project?id=') . $project->project_id ?>" class="color-blue icon-text-link"><ion-icon name='create-outline'></ion-icon>Update</a>
                            <button type='submit' class="btn-text color-danger icon-text-link" name="delete-project"><ion-icon name='trash-outline'></ion-icon>Delete</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        <?php } ?>

        <?php if ($project->project_done == 1) {
            $remark = get_project_remark($project->project_id);
            if (is_response_error($remark)) {
                // review has not been made yet
                if (is_user_trainee()) {
        ?>
                    <span class="color-warning" style="display:flex;align-items:center;gap:6px"><ion-icon name="hourglass-outline"></ion-icon>Awaiting Trainer Review</span>
                <?php } else { ?>
                    <div class="app-btn secondary-btn give-remark-btn" style="width:fit-content;cursor:pointer">Give Remark</div>
                <?php } ?>

            <?php } else { ?>
                <i>
                    <div class="s-project-details">
                        <span>Remark:</span>
                        <div class="s-links">
                            <?php echo $remark->data->remark_desc ?>
                        </div>
                    </div>
                </i>
            <?php } ?>
        <?php } ?>
    </div>


    <p class="error"><?php echo $form_error ?></p>
    <p class="success"><?php echo $form_success ?></p>

    <div class="table-heading">
        <div class="table-heading-top">
            <h3>Project Tasks</h3>

            <div>
                <?php if (is_user_trainee() && $project->project_done == 0) { ?>
                    <button class="app-btn secondary-btn add-task-btn"><ion-icon name='add'></ion-icon> Add Task</button>
                <?php } ?>
            </div>
        </div>
        <div class="table-heading-bottom">
        </div>
    </div>



    <div class="table-h">
        Active Tasks (<?php echo count($ongoing) ?>)
    </div>
    <?php if (count($ongoing) == 0) { ?>
        <div class="empty-list">No Active Tasks</div>
    <?php } else { ?>
        <div class="tasks-list">
            <?php
            $i = 1;
            foreach ($ongoing as $task) {
                $task_name = (is_user_trainer() ? $i++ . ". " : '') . $task->task_name;
            ?>
                <div class="task">
                    <?php
                    if (is_user_trainee()) {
                    ?>
                        <form action="" method="post">
                            <input type="hidden" name="task-id" value="<?php echo $task->task_id ?>">
                            <button name="check-task" type="submit" class="btn-text"><ion-icon name="square-outline" style="color:grey"></ion-icon></button>
                        </form>
                    <?php
                    }
                    ?>
                    <p class="task-name"><?php echo $task_name; ?></p>

                    <?php if (is_user_trainee()) { ?>
                        <div class="task-options">
                            <button class="btn-text color-blue icon-text-link update-task-btn" onclick="setUpdateID(<?php echo $task->task_id ?>)"><ion-icon name='create-outline'></ion-icon> Update</button>
                            <form action="" method="post">
                                <input type="hidden" name="task-id" value="<?php echo $task->task_id ?>">
                                <button name="delete-task" type="submit" class="btn-text color-danger icon-text-link"><ion-icon name='trash-outline'></ion-icon> Delete</butt>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            <?php
            }
            ?>
        </div>
    <?php } ?>


    <div class="spacer"></div>

    <div class="table-h">
        Completed Tasks (<?php echo count($completed) ?>)
    </div>
    <?php if (count($completed) == 0) { ?>
        <div class="empty-list">No Completed Tasks</div>
    <?php } else { ?>
        <div class="tasks-list">
            <?php
            $i = 1;
            foreach ($completed as $task) {
                $task_name = (is_user_trainer() ? $i++ . ". " : '') . $task->task_name;
            ?>
                <div class="task">
                    <?php
                    if (is_user_trainee()) {
                    ?>
                        <form action="" method="post">
                            <input type="hidden" name="task-id" value="<?php echo $task->task_id ?>">
                            <button name="uncheck-task" type="submit" class="btn-text"><ion-icon name="checkbox" style="color:grey"></ion-icon></button>
                        </form>
                    <?php
                    }
                    ?>
                    <p class="task-name"><?php echo $task_name ?></p>

                    <?php if (is_user_trainee()) { ?>
                        <div class="task-options">
                            <button class="btn-text color-blue icon-text-link update-task-btn" onclick="setUpdateID(<?php echo $task->task_id ?>)"><ion-icon name='create-outline'></ion-icon> Update</button>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $task->task_id ?>">
                                <button name="delete-task" type="submit" class="btn-text color-danger icon-text-link"><ion-icon name='trash-outline'></ion-icon> Delete</butt>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            <?php
            }
            ?>
        </div>
    <?php } ?>
</div>

<form action="" method="post">
    <div class="modal add-task-modal">
        <div class="modal-inner">
            <div class="modal-top">
                <h3>Add Task</h3>
                <ion-icon name='close-circle-outline' class="add-task-close"></ion-icon>
            </div>

            <div class="modal-content">
                <?php echo do_shortcode('[input_con name="task-name" label="Task name" error="" placeholder="Enter the task"]') ?>
            </div>

            <button type="submit" name="add-task" class="app-btn primary-btn">Add</button>
        </div>
    </div>
</form>

<form action="" method="post">
    <div class="modal update-task-modal">
        <div class="modal-inner">
            <div class="modal-top">
                <h3>Update Task</h3>
                <ion-icon name='close-circle-outline' class="update-task-close"></ion-icon>
            </div>
            <?php
            if (isset($opened_task)) {

            ?>
                <div class="modal-content">
                    <input type="hidden" name="task-id" value="<?php echo $opened_task->task_id ?>">
                    <?php echo do_shortcode('[input_con name="task-name" label="Task name" error="" placeholder="Enter the task" value="' . $opened_task->task_name . '"]') ?>
                </div>

                <button type="submit" class="app-btn primary-btn" name="update-task">Update</button>
            <?php } else {
                echo
                "<script>
                window.localStorage.setItem('updateTaskOpened', null);
            </script>";
            }
            ?>
        </div>
    </div>
</form>

<?php
if (is_user_trainer()) {
?>
    <form action="" method="post">
        <div class="modal give-remark-modal">
            <div class="modal-inner" style="display:flex;flex-direction:column; gap: 15px;">
                <div class="modal-top">
                    <h3>Add Remark</h3>
                    <ion-icon name='close-circle-outline' class="give-remark-close"></ion-icon>
                </div>

                <div class="modal-content">
                    <input type="hidden" name="project-id" value="<?php echo $project->project_id ?>">
                    <?php echo do_shortcode('[input_con name="remark" label="Remark" error="" placeholder="Enter project remarks" value=""]')
                    ?>
                </div>

                <button type="submit" class="app-btn primary-btn" style="width: 100%" name="give-remark">Give Remark</button>

            </div>
        </div>
    </form>
<?php
}
?>

<?php get_footer() ?>