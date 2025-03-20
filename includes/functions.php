<?php
// Start session if not already started
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Function to check if user is logged in
function is_logged_in() {
    start_session();
    return isset($_SESSION['user_id']);
}

// Function to redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Function to register a new user
function register_user($username, $email, $password) {
    global $conn;
    
    try {
        // Hash the password
        $hashed_password = hash_password($password);
        
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

// Function to authenticate a user
function login_user($username, $password) {
    global $conn;
    
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if (verify_password($password, $user['password'])) {
                start_session();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to log out a user
function logout_user() {
    start_session();
    
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}

// Function to get all tasks for a user
function get_user_tasks($user_id, $status = null) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM tasks WHERE user_id = ?";
        $params = [$user_id];
        
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY due_date ASC, priority DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Function to get a specific task
function get_task($task_id, $user_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

// Function to add a new task
function add_task($user_id, $title, $description, $priority, $due_date) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $priority, $due_date]);
        
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

// Function to update a task
function update_task($task_id, $user_id, $title, $description, $status, $priority, $due_date) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ?, priority = ?, due_date = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $description, $status, $priority, $due_date, $task_id, $user_id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to delete a task
function delete_task($task_id, $user_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to update task status
function update_task_status($task_id, $user_id, $status) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$status, $task_id, $user_id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}
?>