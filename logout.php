<?php
// Include functions
require_once 'includes/functions.php';

// Log out the user
logout_user();

// Set success message
$_SESSION['message'] = 'You have been logged out successfully.';
$_SESSION['message_type'] = 'info';

// Redirect to login page
redirect('login.php');
?>