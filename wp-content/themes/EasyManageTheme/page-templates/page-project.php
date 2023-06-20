<?php if (!is_user_logged_in()) wp_redirect(site_url('/login')) ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $form_error = '';
    $form_success = '';


    $project = get_single_project_new($id);
    if (is_response_error($project)) {
        wp_redirect(site_url('/projects'));
    }

    $tasks = get_tasks($id);

    $ongoing = array_filter($tasks, function ($task) {
        return $task->task_done == 0;
    });
    $completed = array_filter($tasks, function ($task) {
        return $task->task_done == 1;
    });


    if (isset($_GET['task_id'])) {
        $opened_task = get_single_task($_GET['task_id']);
    }
} else {
    wp_redirect(site_url('/projects'));
}

if (isset($_POST['delete-project'])) {
    $res = delete_project($id);

    if (is_response_error($res)) {
        $form_error = $res->message ?? "Deletion Failed";
    } else {
        $form_success = "Successfully Deleted";
    }

    do_action('on_project_delete');
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
        $form_success = "Successfully Created";

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
        $form_success = "Successfully Updated";
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
        $form_success = "Successfully Updated";
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
        $form_success = "Successfully Updated";
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
        $form_success = "Successfully Deleted";
        $tasks = get_tasks($id);

        $ongoing = array_filter($tasks, function ($task) {
            return $task->task_done == 0;
        });
        $completed = array_filter($tasks, function ($task) {
            return $task->task_done == 1;
        });
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
                $assignees = explode(",", $project->project_assignees);
                foreach ($assignees as $assignee) {
                ?>
                    <div class="user-icon">
                        <?php
                        $temp = get_user_by('id', (int)$assignee);
                        $temp = get_user_meta($assignee, 'fullname', true);
                        echo get_initials($temp);
                        ?>
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
        <div class="s-project-details" style="align-items: flex-start;">
            <span>Description:</span>
            <div>
                <?php echo $project->project_description ?>
            </div>
        </div>
        <form action="" method="post">
            <div class="s-project-details">
                <span>Actions:</span>
                <div class="s-links">
                    <button class="btn-text color-success icon-text-link"><ion-icon name='checkmark-circle-outline'></ion-icon>Mark As Complete</button>
                    <?php if (is_user_trainer()) { ?>
                        <a href="<?php echo site_url('/projects/update-project?id=') . $project->project_id ?>" class="color-blue icon-text-link"><ion-icon name='create-outline'></ion-icon>Update</a>
                        <button type='submit' class="btn-text color-danger icon-text-link" name="delete-project"><ion-icon name='trash-outline'></ion-icon>Delete</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>


    <p class="error"><?php echo $form_error ?></p>
    <p class="success"><?php echo $form_success ?></p>

    <div class="table-heading">
        <div class="table-heading-top">
            <h3>Task</h3>

            <div>
                <!-- <form action="" method="get">
                    <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                    ?>
                </form> -->
                <?php if (is_user_trainee()) { ?>
                    <button class="app-btn secondary-btn add-task-btn"><ion-icon name='add'></ion-icon> Add Task</button>
                <?php } ?>
            </div>
        </div>
        <div class="table-heading-bottom">
            <!-- <form action="" method="get">
                <?php // echo do_shortcode('[search_bar placeholder="search"]') 
                ?>
            </form> -->
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
            foreach ($ongoing as $task) {
            ?>
                <div class="task">
                    <form action="" method="post">
                        <input type="hidden" name="task-id" value="<?php echo $task->task_id ?>">
                        <button name="check-task" type="submit" class="btn-text"><ion-icon name="square-outline" style="color:grey"></ion-icon></button>
                    </form>
                    <p class="task-name"><?php echo $task->task_name ?></p>

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
            foreach ($completed as $task) {
            ?>
                <div class="task">
                    <form action="" method="post">
                        <input type="hidden" name="task-id" value="<?php echo $task->task_id ?>">
                        <button name="uncheck-task" type="submit" class="btn-text"><ion-icon name="checkbox" style="color:grey"></ion-icon></button>
                    </form>
                    <p class="task-name"><?php echo $task->task_name ?></p>

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

<?php get_footer() ?>