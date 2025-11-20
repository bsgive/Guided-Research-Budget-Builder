<?php
session_start();
require 'database.php';

$signupErrors = [];
$signupSuccess = '';
$loginErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $signupErrors[] = "All registration fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signupErrors[] = "Please enter a valid email address.";
    } elseif ($password !== $confirm) {
        $signupErrors[] = "Passwords do not match.";
    }

    if (empty($signupErrors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $signupErrors[] = "An account with that email already exists.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $passwordHash);

            if ($stmt->execute()) {
                $signupSuccess = "Account created successfully. You can now sign in.";
            } else {
                $signupErrors[] = "Error creating account. Please try again.";
            }
        }
        $stmt->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if ($email === '' || $password === '') {
        $loginErrors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
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
                $loginErrors[] = "Incorrect email or password.";
            }
        } else {
            $loginErrors[] = "Incorrect email or password.";
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
            <form action="userManagement.php" method="POST" class="SignUp">
                <div class="form-group">
                    <h2>User Sign Up</h2>
                    <div class="form-entry">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-entry">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-entry">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-entry">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <?php if (!empty($signupErrors)): ?>
                        <?php foreach ($signupErrors as $e): ?>
                            <p class="error"><?php echo htmlspecialchars($e); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($signupSuccess)): ?>
                        <p class="success"><?php echo htmlspecialchars($signupSuccess); ?></p>
                    <?php endif; ?>

                    <div>
                        <button type="submit" name="signup" class="bottom">Create Account</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="phpDoc2">
            <form action="userManagement.php" method="POST" class="SignUp">
                <div class="form-group">
                    <h2>User login</h2>
                    <div class="form-entry">
                        <label for="login_email">Email:</label>
                        <input type="email" id="login_email" name="login_email" required>
                    </div>
                    <div class="form-entry">
                        <label for="login_password">Password:</label>
                        <input type="password" id="login_password" name="login_password" required>
                    </div>

                    <?php if (!empty($loginErrors)): ?>
                        <?php foreach ($loginErrors as $e): ?>
                            <p class="error"><?php echo htmlspecialchars($e); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div>
                        <button type="submit" name="login" class="bottom">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>