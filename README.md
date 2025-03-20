# PHP Task Management System

A simple task management system built with PHP and MySQL that allows users to create, manage, and track their tasks.

## Features

- User authentication (register, login, logout)
- Task management (create, read, update, delete)
- Task filtering by status (all, pending, in progress, completed)
- Task prioritization (low, medium, high)
- Responsive design using Bootstrap 5

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Installation

1. **Clone the repository or download the files**

2. **Set up the database**
   - Import the `config/database.sql` file into your MySQL server
   - This will create the database and tables for the application

3. **Configure the database connection**
   - Open `config/database.php`
   - Update the database credentials to match your environment:
     ```php
     $host = 'localhost';     // Your database host
     $dbname = 'task_management'; // Database name
     $username = 'root';      // Database username
     $password = '';          // Database password
     ```

4. **Start your web server**
   - Make sure your web server is pointing to the project directory

5. **Access the application**
   - Open your browser and navigate to the project URL
   - You should see the login page

## Usage

### User Registration and Login
- New users can register by providing a username, email, and password
- Existing users can log in using their username and password
- A sample user account is pre-created:
  - Username: testuser
  - Password: password123

### Managing Tasks
- **View Tasks**: The dashboard displays all your tasks, which can be filtered by status
- **Add Task**: Click the "Add New Task" button to create a new task
- **Edit Task**: Click the "Edit" option in the task's dropdown menu
- **Change Status**: Use the dropdown menu to mark tasks as "Pending", "In Progress", or "Completed"
- **Delete Task**: Use the dropdown menu to delete a task

### Task Properties
- **Title**: A brief name for your task (required)
- **Description**: Detailed information about the task
- **Status**: The current state of the task (pending, in progress, completed)
- **Priority**: The importance level of the task (low, medium, high)
- **Due Date**: The deadline for completing the task

## Project Structure

```
task_management/
├── config/
│   ├── database.php           # Database connection
│   └── database.sql           # SQL schema
├── includes/
│   ├── functions.php          # Utility functions
│   ├── header.php             # Common header
│   └── footer.php             # Common footer
├── add_task.php               # Add task form
├── edit_task.php              # Edit task form
├── index.php                  # Dashboard/Task listing
├── login.php                  # User login
├── logout.php                 # User logout
├── register.php               # User registration
└── README.md                  # Project documentation
```

## Security Notes

This application implements basic security practices:
- Password hashing using PHP's password_hash()
- Input sanitization for all user inputs
- Prepared statements for database queries
- Session-based authentication
- CSRF protection via form submission

For production use, additional security measures should be considered, such as:
- HTTPS implementation
- Enhanced password policies
- Rate limiting for login attempts
- Additional validation and sanitization

## License

This project is open-source and available for personal and educational use.