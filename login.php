<?php
// Include header
require_once 'includes/header.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Initialize variables
$username = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    
    // If no errors, attempt to log in
    if (empty($errors)) {
        if (login_user($username, $password)) {
            // Set success message
            $_SESSION['message'] = 'Login successful!';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to dashboard
            redirect('index.php');
        } else {
            // Set error message
            $errors['login'] = 'Invalid username or password';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <?php if (isset($errors['login'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['login']; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>