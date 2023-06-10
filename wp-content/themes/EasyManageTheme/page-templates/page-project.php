<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $project = get_single_project($id);

    $tasks = get_tasks($id);

    $ongoing = array_filter($tasks, function ($task) {
        return $task->done == 0;
    });
    $completed = array_filter($tasks, function ($task) {
        return $task->done == 1;
    });


    if (isset($_GET['task_id'])) {
        $opened_task = get_single_task($_GET['task_id']);
    }
} else {
    wp_redirect(site_url('/projects'));
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
        <span>/ <?php echo $project->name ?></span>
    </div>

    <div class="s-project-info">
        <div class="s-project-title"><?php echo $project->name ?></div>
        <div class="s-project-details">
            <span>Assignees:</span>
            <div>
                <div class="user-icon">BK</div>
                <div class="user-icon">TJ</div>
            </div>
        </div>
        <div class="s-project-details">
            <span>Deadline:</span>
            <div>
                18th June 2022
            </div>
        </div>
        <div class="s-project-details">
            <span>Category:</span>
            <div>
                Web App
            </div>
        </div>
        <div class="s-project-details">
            <span>Actions:</span>
            <div class="s-links">
                <span class="color-success icon-text-link"><ion-icon name='checkmark-circle-outline'></ion-icon> Mark As Complete</span>
                <span class="color-blue icon-text-link"><ion-icon name='create-outline'></ion-icon> Update</span>
                <span class="color-danger icon-text-link"><ion-icon name='trash-outline'></ion-icon> Delete</span>
            </div>
        </div>
    </div>

    <div class="table-heading">
        <h3>Tasks</h3>
        <div>
            <!-- <form action="" method="get">
                <?php // echo do_shortcode('[search_bar placeholder="Search"]') 
                ?>
            </form> -->
            <button class="app-btn secondary-btn add-task-btn"><ion-icon name='add'></ion-icon> Add Task</button>
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
                        <input type="hidden" name="id" value="<?php echo $task->id ?>">
                        <button name="check-task" type="submit" class="btn-text"><ion-icon name="square-outline" style="color:grey"></ion-icon></button>
                    </form>
                    <p class="task-name"><?php echo $task->name ?></p>

                    <div class="task-options">
                        <button class="btn-text color-blue icon-text-link update-task-btn" onclick="setUpdateID(<?php echo $task->id ?>)"><ion-icon name='create-outline'></ion-icon> Update</button>
                        <form action="" method="post">
                            <input type="hidden" name="ids" value="<?php echo $task->id ?>">
                            <button name="delete-task" type="submit" class="btn-text color-danger icon-text-link"><ion-icon name='trash-outline'></ion-icon> Delete</butt>
                        </form>
                    </div>
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
                        <input type="hidden" name="id" value="<?php echo $task->id ?>">
                        <button name="check-task" type="submit" class="btn-text"><ion-icon name="checkbox" style="color:grey"></ion-icon></button>
                    </form>
                    <p class="task-name"><?php echo $task->name ?></p>

                    <div class="task-options">
                        <button class="btn-text color-blue icon-text-link update-task-btn" onclick="setUpdateID(<?php echo $task->id ?>)"><ion-icon name='create-outline'></ion-icon> Update</button>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $task->id ?>">
                            <button name="delete-task" type="submit" class="btn-text color-danger icon-text-link"><ion-icon name='trash-outline'></ion-icon> Delete</butt>
                        </form>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    <?php } ?>
</div>

<div class="modal add-task-modal">
    <div class="modal-inner">
        <div class="modal-top">
            <h3>Add Task</h3>
            <ion-icon name='close-circle-outline' class="add-task-close"></ion-icon>
        </div>

        <div class="modal-contact">
            <p>This is the modal content.</p>
        </div>
    </div>
</div>


<div class="modal update-task-modal">
    <div class="modal-inner">
        <div class="modal-top">
            <h3>Update Task</h3>
            <ion-icon name='close-circle-outline' class="update-task-close"></ion-icon>
        </div>
        <?php
        if (isset($opened_task)) {

        ?>
            <div class="modal-contact">
                <p>This is the modal content to update <?php var_dump($opened_task) ?> .</p>
            </div>
        <?php } else {
            echo
            "<script>
                window.localStorage.setItem('updateTaskOpened', null);
            </script>";
        }
        ?>
    </div>
</div>

<?php get_footer() ?>