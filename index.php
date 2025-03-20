<?php
// Include header
require_once 'includes/header.php';

// Redirect to login page if not logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get filter parameter (default: all)
$filter = sanitize_input($_GET['filter'] ?? 'all');

// Get tasks based on filter
if ($filter === 'all') {
    $tasks = get_user_tasks($user_id);
    $active_filter = 'all';
} else {
    $tasks = get_user_tasks($user_id, $filter);
    $active_filter = $filter;
}

// Process task actions (mark as in progress, completed, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['task_id'])) {
    $action = sanitize_input($_POST['action']);
    $task_id = (int)$_POST['task_id'];
    
    if ($action === 'delete') {
        if (delete_task($task_id, $user_id)) {
            $_SESSION['message'] = 'Task deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete task.';
            $_SESSION['message_type'] = 'danger';
        }
    } elseif (in_array($action, ['pending', 'in_progress', 'completed'])) {
        if (update_task_status($task_id, $user_id, $action)) {
            $_SESSION['message'] = 'Task status updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update task status.';
            $_SESSION['message_type'] = 'danger';
        }
    }
    
    // Redirect to refresh the page and avoid form resubmission
    redirect('index.php?filter=' . $active_filter);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Tasks</h1>
    <a href="add_task.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Task
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-light">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link <?php echo $active_filter === 'all' ? 'active' : ''; ?>" href="index.php?filter=all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_filter === 'pending' ? 'active' : ''; ?>" href="index.php?filter=pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_filter === 'in_progress' ? 'active' : ''; ?>" href="index.php?filter=in_progress">In Progress</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_filter === 'completed' ? 'active' : ''; ?>" href="index.php?filter=completed">Completed</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if (empty($tasks)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No tasks found.
                <?php if ($active_filter !== 'all'): ?>
                    <a href="index.php?filter=all">View all tasks</a>
                <?php else: ?>
                    <a href="add_task.php">Add your first task</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($tasks as $task): ?>
                    <?php
                    // Set priority class
                    $priority_class = '';
                    switch ($task['priority']) {
                        case 'high':
                            $priority_class = 'priority-high';
                            break;
                        case 'medium':
                            $priority_class = 'priority-medium';
                            break;
                        case 'low':
                            $priority_class = 'priority-low';
                            break;
                    }
                    
                    // Set status class
                    $status_class = 'status-' . $task['status'];
                    
                    // Check if due date is approaching
                    $due_date_class = '';
                    if (!empty($task['due_date'])) {
                        $due_date = new DateTime($task['due_date']);
                        $today = new DateTime();
                        $diff = $today->diff($due_date);
                        
                        if ($due_date < $today) {
                            $due_date_class = 'due-date-soon';
                        } elseif ($diff->days <= 2) {
                            $due_date_class = 'due-date-soon';
                        }
                    }
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card task-card mb-3 <?php echo $priority_class . ' ' . $status_class; ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-truncate" style="max-width: 80%;"><?php echo htmlspecialchars($task['title']); ?></h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="taskActions<?php echo $task['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="taskActions<?php echo $task['id']; ?>">
                                        <li>
                                            <a class="dropdown-item" href="edit_task.php?id=<?php echo $task['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </li>
                                        <?php if ($task['status'] !== 'pending'): ?>
                                            <li>
                                                <form action="index.php?filter=<?php echo $active_filter; ?>" method="POST">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <input type="hidden" name="action" value="pending">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-clock"></i> Mark as Pending
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($task['status'] !== 'in_progress'): ?>
                                            <li>
                                                <form action="index.php?filter=<?php echo $active_filter; ?>" method="POST">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <input type="hidden" name="action" value="in_progress">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-spinner"></i> Mark as In Progress
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($task['status'] !== 'completed'): ?>
                                            <li>
                                                <form action="index.php?filter=<?php echo $active_filter; ?>" method="POST">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <input type="hidden" name="action" value="completed">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check"></i> Mark as Completed
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="index.php?filter=<?php echo $active_filter; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <?php 
                                    // Show description or truncate if too long
                                    $description = htmlspecialchars($task['description']);
                                    if (strlen($description) > 100) {
                                        echo substr($description, 0, 100) . '...';
                                    } else {
                                        echo $description;
                                    }
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'success'); ?>">
                                        <?php echo ucfirst($task['priority']); ?> Priority
                                    </span>
                                    <span class="badge bg-<?php echo $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'info' : 'secondary'); ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($task['status'])); ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (!empty($task['due_date'])): ?>
                                <div class="card-footer text-muted">
                                    <small class="<?php echo $due_date_class; ?>">
                                        <i class="far fa-calendar-alt"></i> Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>