<?php
// Include database connection
require_once 'config/database.php';
// Include functions
require_once 'includes/functions.php';

// Start session
start_session();

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .task-card {
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .priority-high {
            border-left: 5px solid #dc3545;
        }
        .priority-medium {
            border-left: 5px solid #ffc107;
        }
        .priority-low {
            border-left: 5px solid #28a745;
        }
        .status-pending {
            background-color: #f8f9fa;
        }
        .status-in_progress {
            background-color: #e2f0fd;
        }
        .status-completed {
            background-color: #e8f5e9;
        }
        .due-date-soon {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">Task Management</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <?php if (is_logged_in()): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($current_page == 'add_task.php') ? 'active' : ''; ?>" href="add_task.php">Add Task</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="login.php">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($current_page == 'register.php') ? 'active' : ''; ?>" href="register.php">Register</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <?php if (is_logged_in()): ?>
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo $_SESSION['username']; ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <main>