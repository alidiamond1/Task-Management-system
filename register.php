<?php
// Include header
require_once 'includes/header.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Initialize variables
$username = $email = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors['username'] = 'Username must be between 3 and 50 characters';
    }
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }
    
    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        $user_id = register_user($username, $email, $password);
        
        if ($user_id) {
            // Set success message
            $_SESSION['message'] = 'Registration successful! You can now log in.';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to login page
            redirect('login.php');
        } else {
            // Set error message
            $errors['registration'] = 'Registration failed. Username or email may already be taken.';
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary text-center py-4">
                    <h3 class="font-weight-light my-2 text-white">Create Account</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($errors['registration'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['registration']; ?>
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
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" placeholder="name@example.com"
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                            <label for="email"><i class="fas fa-envelope text-muted me-2"></i>Email address</label>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           id="password" name="password" placeholder="Create a password" required>
                                    <label for="password"><i class="fas fa-lock text-muted me-2"></i>Password</label>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                           id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                                    <label for="confirm_password"><i class="fas fa-lock text-muted me-2"></i>Confirm Password</label>
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['confirm_password']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Already have an account? <a href="login.php" class="text-primary text-decoration-none">Sign in!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

