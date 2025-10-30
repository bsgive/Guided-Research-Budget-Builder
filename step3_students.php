<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['student_id']) && isset($_POST['fte'])) {

        $fte = floatval($_POST['fte']);
        if ($fte > 50) {
            $fte = 50;
        }

     
        $_SESSION['students'][] = [
            'student_id' => $_POST['student_id'],
            'fte'        => $fte
        ];

 
        header("Location: step4_travel.php");
        exit;

    } else {
        $error = "Please select a student and enter FTE.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Home — My Website</title>
    <link rel="stylesheet" href="stylesheets.css?v=2" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap" rel="stylesheet">
</head>
<body>
    <div class = "header">
        <div class ="budgets">
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

    <h2>Step 3: Student Appointments</h2>
    <div class="phpDoc1">
        <?php
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
            <form action="step3_students.php" method="POST" class="form-group">
                <div class="form-entry">
                    <label for="student_id">Student:</label>
                    <select name="student_id" id="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php
                        $students = $conn->query("SELECT sid, name FROM students ORDER BY name");
                        while ($row = $students->fetch_assoc()) {
                            echo "<option value='{$row['sid']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-entry">
                    <label>FTE (max 50%):</label>
                    <input type="number" name="fte" max="50" min="0" required>
                </div>
                <div class="form-entry">
                    <button type="submit" class="bottom">Next →</button>
                </div>
            </form>
    </div>
</body>
</html>
