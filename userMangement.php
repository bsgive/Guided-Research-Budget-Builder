<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    //We need to add a users to the database which contains these values + Sid and Id to connect whether they are a student or not

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = "All registration fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "An account with that email already exists.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $passwordHash);

            if ($stmt->execute()) {
                $success = "Account created successfully. You can now sign in.";
            } else {
                $errors[] = "Error creating account. Please try again.";
            }
        }
        $stmt->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $userName, $hash);

        if ($stmt->fetch()) {
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $userName;
                header("Location: step1_plan.php");
                exit;
            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign In / Create Account</title>
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
            <a class="link" href="index.html">Home</a>
            <a class="link" href="features.html">Features</a>
            <a class="link" href="about.html">About</a>
            <a class="link" href="contact.html">Contact</a>
        </div>
    </div>
    <div class="rowInput">
        <div class="phpDoc2">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $e): ?>
                    <p class="error"><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form action="userManagement.php" method="POST" class="SignUp">
                <div class="form-group">
                    <h2>User Sign Up</h2>
                    <div class="form-entry">
                        <label for="title">Project Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="smallInputs">
                        <div class="form-entry">
                            <label for="pi_id">Principal Investigator:</label>
                            <select id="pi_id" name="pi_id" required>
                                <option value="">-- Select PI --</option>
                                <?php
                                $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
                                while ($row = $faculty->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-entry">
                            <label for="start_year">Start Year:</label>
                            <select id="start_year" name="start_year" class="startYear" required>
                                <?php
                                $current_year = date("Y");
                                for ($i = 0; $i < 5; $i++) {
                                    $year = $current_year + $i;
                                    echo "<option value='$year'>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-entry">
                            <label for="duration">Project Duration:</label>
                            <select id="duration" name="duration" required>
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo "<option value='$i'>$i year(s)</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="rowInput">
                        <div class="form-entry_description">
                            <div>
                                <label for="description">Project Description (optional):</label>
                            </div>
                            <div>
                                <textarea id="description" name="description" rows="4" cols="40"></textarea>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="bottom">Next →</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="phpDoc2">
            <form action="userManagement.php" method="POST" class="SignUp">
                <div class="form-group">
                    <h2>User login</h2>
                    <div class="form-entry">
                        <label for="title">Project Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="smallInputs">
                        <div class="form-entry">
                            <label for="pi_id">Principal Investigator:</label>
                            <select id="pi_id" name="pi_id" required>
                                <option value="">-- Select PI --</option>
                                <?php
                                $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
                                while ($row = $faculty->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-entry">
                            <label for="start_year">Start Year:</label>
                            <select id="start_year" name="start_year" class="startYear" required>
                                <?php
                                $current_year = date("Y");
                                for ($i = 0; $i < 5; $i++) {
                                    $year = $current_year + $i;
                                    echo "<option value='$year'>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-entry">
                            <label for="duration">Project Duration:</label>
                            <select id="duration" name="duration" required>
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo "<option value='$i'>$i year(s)</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="rowInput">
                        <div class="form-entry_description">
                            <div>
                                <label for="description">Project Description (optional):</label>
                            </div>
                            <div>
                                <textarea id="description" name="description" rows="4" cols="40"></textarea>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="bottom">Next →</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>