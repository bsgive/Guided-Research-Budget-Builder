<?php
session_start();
require 'database.php';

//Ensure is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: userManagement.php");
    exit;
}

//Error Handles
$adminAddErrors = [];
$adminAddSuccess = '';
$facultyAddErrors = [];
$facultyAddSuccess = '';
$studentAddErrors = [];
$studentAddSuccess = '';
$deleteMessage = '';

//Delete User
if (isset($_GET['delete_user'])) {
    $userId = (int)$_GET['delete_user'];
    if ($userId != 1) { 
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $deleteMessage = "User deleted successfully.";
        } else {
            $deleteMessage = "Error deleting user.";
        }
        $stmt->close();
    }
}
//Delete Faculty
if (isset($_GET['delete_faculty'])) {
    $facultyId = (int)$_GET['delete_faculty'];
    $stmt = $conn->prepare("DELETE FROM faculty_staff WHERE id = ?");
    $stmt->bind_param("i", $facultyId);

    if ($stmt->execute()) {
        $deleteMessage = "Faculty deleted successfully.";
    } else {
        $deleteMessage = "Error deleting faculty.";
    }
    $stmt->close();
}
//Delete Student
if (isset($_GET['delete_student'])) {
    $studentId = (int)$_GET['delete_student'];
    $stmt = $conn->prepare("DELETE FROM students WHERE sid = ?");
    $stmt->bind_param("i", $studentId);
    if ($stmt->execute()) {
        $deleteMessage = "Student deleted successfully.";
    } else {
        $deleteMessage = "Error deleting student.";
    }
    $stmt->close();
}
//Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_add_user'])) {
    $name = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : '';
    $email = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '';
    $password = $_POST['admin_password'];

    if ($name === '' || $email === '' || $password === '') {
        $adminAddErrors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $adminAddErrors[] = "Invalid email format.";
    }

    if (empty($adminAddErrors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $adminAddErrors[] = "Email already exists.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $passwordHash);

            if ($stmt->execute()) {
                $adminAddSuccess = "User added!";
            } else {
                $adminAddErrors[] = "Failed to add user.";
            }
        }
        $stmt->close();
    }
}
//Add Faculty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_add_faculty'])) {
    $name = !empty($_POST['faculty_name']) ? trim($_POST['faculty_name']) : '';
    $base_salary = trim($_POST['faculty_salary']);
    $position = isset($_POST['faculty_position']) ? $_POST['faculty_position'] : '';

    if (empty($name)) {
        $facultyAddErrors[] = "Name required.";
    }
    if (empty($base_salary) || !is_numeric($base_salary)) {
        $facultyAddErrors[] = "Enter valid salary.";
    }
    if (empty($position)) {
        $facultyAddErrors[] = "Position required.";
    }

    if (empty($facultyAddErrors)) {
        $stmt = $conn->prepare("SELECT id FROM faculty_staff WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $facultyAddErrors[] = "Faculty member already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO faculty_staff (name, base_salary, position) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $name, $base_salary, $position);

            if ($stmt->execute()) {
                $facultyAddSuccess = "Added faculty member.";
            } else {
                $facultyAddErrors[] = "Could not add faculty.";
            }
        }
        $stmt->close();
    }
}
//Add Student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_add_student'])) {
    $name = trim($_POST['student_name']);
    $residency = isset($_POST['student_residency']) ? $_POST['student_residency'] : '';

    if (empty($name)) {
        $studentAddErrors[] = "Student name is required.";
    }
    if (empty($residency)) {
        $studentAddErrors[] = "Select residency status.";
    }

    if (empty($studentAddErrors)) {
        $stmt = $conn->prepare("SELECT sid FROM students WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $studentAddErrors[] = "Student name already in system.";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (name, residency_status) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $residency);

            if ($stmt->execute()) {
                $studentAddSuccess = "Student added.";
            } else {
                $studentAddErrors[] = "Unable to add student.";
            }
        }
        $stmt->close();
    }
}

//Users
$usersResult = $conn->query("SELECT id, name, email FROM users ORDER BY id DESC");
$facultyResult = $conn->query("SELECT id, name, position, base_salary FROM faculty_staff ORDER BY name");
$studentsResult = $conn->query("SELECT sid, name, residency_status FROM students ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Admin Panel - Research Budget Builder</title>
        <link rel="stylesheet" href="stylesheets.css?v=2" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap"
            rel="stylesheet">
    </head>

    <body>
        <div class="header">
            <div class="budgets">
                <img src="assets/logo.png" alt="Logo" class="Logo">
                <p class="budgetsText">Research Budget Builder</p>
            </div>

            <div class="nav-links">
                <a class="link" href="step1_plan.php">Budget Builder</a>
                <a class="link" href="logout.php">Logout</a>
            </div>
        </div>
    <h2>Admin</h2>
        <div class="phpDoc1" id="adminBlock">
            <div class="admin-section">
                <h2>Add New User</h2>
                <form action="admin.php" method="POST" class="SignUp">
                    <div class="form-group">
                        <div class="form-entry">
                            <label for="admin_name">Full Name:</label>
                            <input type="text" id="admin_name" name="admin_name" required>
                        </div>
                        <div class="form-entry">
                            <label for="admin_email">Email:</label>
                            <input type="email" id="admin_email" name="admin_email" required>
                        </div>
                        <div class="form-entry">
                            <label for="admin_password">Password:</label>
                            <input type="password" id="admin_password" name="admin_password" required>
                        </div>

                        <?php if (!empty($adminAddErrors)): ?>
                            <?php foreach ($adminAddErrors as $e): ?>
                                <p class="error"><?php echo htmlspecialchars($e); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($adminAddSuccess)): ?>
                            <p class="success"><?php echo htmlspecialchars($adminAddSuccess); ?></p>
                        <?php endif; ?>

                        <div>
                            <button type="submit" name="admin_add_user" class="bottom">Add User to Database</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="admin-section">
            <h2>All Users</h2>
                <table class="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersResult && $usersResult->num_rows > 0): ?>
                            <?php while ($user = $usersResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php if ($user['id'] != 1): ?>
                                            <a href="admin.php?delete_user=<?php echo $user['id']; ?>" 
                                            onclick="return confirm('Are you sure you want to delete this user?');" 
                                            class="delete-link">Delete</a>
                                        <?php else: ?>
                                            <span style="color: #999;">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="phpDoc1" id="adminBlock">
                <div class="admin-section">
                    <h2>Add New Faculty/Staff</h2>
                    <form action="admin.php" method="POST" class="SignUp">
                        <div class="form-group">
                            <div class="form-entry">
                                <label for="faculty_name">Faculty/Staff Name:</label>
                                <input type="text" id="faculty_name" name="faculty_name" required>
                            </div>
                            <div class="form-entry">
                                <label for="faculty_salary">Base Salary ($):</label>
                                <input type="number" id="faculty_salary" name="faculty_salary" step="0.01" min="0" required>
                            </div>
                            <div class="form-entry">
                                <label for="faculty_position">Position:</label>
                                <input type="text" id="faculty_position" name="faculty_position" required>
                            </div>

                            <?php if (!empty($facultyAddErrors)): ?>
                                <?php foreach ($facultyAddErrors as $e): ?>
                                    <p class="error"><?php echo htmlspecialchars($e); ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($facultyAddSuccess)): ?>
                                <p class="success"><?php echo htmlspecialchars($facultyAddSuccess); ?></p>
                            <?php endif; ?>

                            <div>
                                <button type="submit" name="admin_add_faculty" class="bottom">Add Faculty to Database</button>
                            </div>
                        </div>
                    </form>
                </div>
            <div class="admin-section">
                <h2>All Faculty/Staff</h2>
                <table class="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Base Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($facultyResult && $facultyResult->num_rows > 0): ?>
                            <?php while ($faculty = $facultyResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($faculty['id']); ?></td>
                                    <td><?php echo htmlspecialchars($faculty['name']); ?></td>
                                    <td><?php echo htmlspecialchars(isset($faculty['position']) ? $faculty['position'] : 'N/A'); ?></td>
                                    <td>$<?php echo number_format(isset($faculty['base_salary']) ? $faculty['base_salary'] : 0, 2); ?></td>
                                    <td>
                                        <a href="admin.php?delete_faculty=<?php echo $faculty['id']; ?>" 
                                        onclick="return confirm('Are you sure you want to delete this faculty member?');" 
                                        class="delete-link">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No faculty found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="phpDoc1" id="adminBlock">
            <div class="admin-section">
                <h2>Add New Student</h2>
                <form action="admin.php" method="POST" class="SignUp">
                    <div class="form-group">
                        <div class="form-entry">
                            <label for="student_name">Student Name:</label>
                            <input type="text" id="student_name" name="student_name" required>
                        </div>
                        <div class="form-entry">
                            <label for="student_residency">Residency Status:</label>
                            <select id="student_residency" name="student_residency" required>
                                <option value="">-- Select Residency --</option>
                                <option value="in-state">In-State</option>
                                <option value="out-of-state">Out-of-State</option>
                            </select>
                        </div>

                        <?php if (!empty($studentAddErrors)): ?>
                            <?php foreach ($studentAddErrors as $e): ?>
                                <p class="error"><?php echo htmlspecialchars($e); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($studentAddSuccess)): ?>
                            <p class="success"><?php echo htmlspecialchars($studentAddSuccess); ?></p>
                        <?php endif; ?>

                        <div>
                            <button type="submit" name="admin_add_student" class="bottom">Add Student to Database</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="admin-section">
                <h2>All Students</h2>
                <table class="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Residency Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($studentsResult && $studentsResult->num_rows > 0): ?>
                            <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['sid']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars(isset($student['residency_status']) ? $student['residency_status'] : 'N/A'); ?></td>
                                    <td>
                                        <a href="admin.php?delete_student=<?php echo $student['sid']; ?>" 
                                        onclick="return confirm('Are you sure you want to delete this student?');" 
                                        class="delete-link">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
