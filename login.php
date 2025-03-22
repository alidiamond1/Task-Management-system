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

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary text-center py-4">
                    <h3 class="font-weight-light my-2 text-white">Welcome Back!</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($errors['login'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['login']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                   id="username" name="username" placeholder="Username"
                                   value="<?php echo htmlspecialchars($username); ?>" required>
                            <label for="username"><i class="fas fa-user text-muted me-2"></i>Username</label>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password" placeholder="Password" required>
                            <label for="password"><i class="fas fa-lock text-muted me-2"></i>Password</label>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <i class="fas fa-user-plus me-2"></i>
                        Need an account? <a href="register.php" class="text-primary text-decoration-none">Sign up now!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

