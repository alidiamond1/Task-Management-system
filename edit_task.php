<?php
// Include header
require_once 'includes/header.php';

// Redirect to login page if not logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if task ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'Invalid task ID.';
    $_SESSION['message_type'] = 'danger';
    redirect('index.php');
}

$task_id = (int)$_GET['id'];

// Get task data
$task = get_task($task_id, $user_id);

// Check if task exists and belongs to the user
if (!$task) {
    $_SESSION['message'] = 'Task not found or you do not have permission to edit it.';
    $_SESSION['message_type'] = 'danger';
    redirect('index.php');
}

// Initialize variables with task data
$title = $task['title'];
$description = $task['description'];
$status = $task['status'];
$priority = $task['priority'];
$due_date = $task['due_date'];
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $status = sanitize_input($_POST['status'] ?? 'pending');
    $priority = sanitize_input($_POST['priority'] ?? 'medium');
    $due_date = sanitize_input($_POST['due_date'] ?? '');
    
    // Validate title
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    } elseif (strlen($title) > 100) {
        $errors['title'] = 'Title must be less than 100 characters';
    }

    // Validate description
    if (strlen($description) > 500) {
        $errors['description'] = 'Description must be less than 500 characters';
    }
    
    // Validate status
    if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
        $errors['status'] = 'Invalid status';
    }
    
    // Validate priority
    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $errors['priority'] = 'Invalid priority';
    }
    
    // Validate due date
    if (!empty($due_date)) {
        $date = DateTime::createFromFormat('Y-m-d', $due_date);
        if (!$date || $date->format('Y-m-d') !== $due_date) {
            $errors['due_date'] = 'Invalid date format';
        }
    }
    
    // If no errors, update the task
    if (empty($errors)) {
        if (update_task($task_id, $user_id, $title, $description, $status, $priority, $due_date)) {
            // Set success message
            $_SESSION['message'] = 'Task updated successfully!';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to dashboard
            redirect('index.php');
        } else {
            // Set error message
            $errors['task'] = 'Failed to update task. Please try again.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Task</h4>
            </div>
            <div class="card-body">
                <?php if (isset($errors['task'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['task']; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $task_id); ?>" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" id="status" name="status">
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['status']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select <?php echo isset($errors['priority']) ? 'is-invalid' : ''; ?>" id="priority" name="priority">
                            <option value="low" <?php echo $priority === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $priority === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $priority === 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                        <?php if (isset($errors['priority'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['priority']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control <?php echo isset($errors['due_date']) ? 'is-invalid' : ''; ?>" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>">
                        <?php if (isset($errors['due_date'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['due_date']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>