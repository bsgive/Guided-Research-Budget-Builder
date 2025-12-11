<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['students'] = [];
    
    if (!empty($_POST['students']) && is_array($_POST['students'])) {
        foreach ($_POST['students'] as $s) {
            if (empty($s['student_id'])) {
                continue;
            }
            
            $fte = isset($s['fte']) ? floatval($s['fte']) : 0;
            if ($fte > 50) {
                $fte = 50;
            }
            
            $_SESSION['students'][] = [
                'student_id' => $s['student_id'],
                'fte' => $fte
            ];
        }
    }
    
    header("Location: step4_travel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <script>
        function addStudent() {
            const container = document.getElementById('student_container');
            const index = container.children.length + 1;

            let html = `<div class="co_pi_block">
        <label>Student:</label>
        <select name="students[${index}][student_id]" required>
            <option value="">-- Select Student --</option>
            <?php
            $students_js = $conn->query("SELECT sid, name FROM students ORDER BY name");
            while ($row = $students_js->fetch_assoc()) {
                echo "document.write('<option value=\"{$row['sid']}\">{$row['name']}</option>');";}
            ?>
        </select>
        <label>FTE (max 50%):</label>
        <input type="number" name="students[${index}][fte]" min="0" max="50" required>
        <button type="button" onclick="this.parentElement.remove()">Remove Student</button>
    </div>`;

            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Home — My Website</title>
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

    <h2>Step 3: Student Appointments</h2>
    <div class="phpDoc1">
        <?php
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
        <form action="step3_students.php" method="POST" class="form-group">
            <div class="form-entry">
                <label>Student:</label>
                <select name="students[0][student_id]">
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
                <input type="number" name="students[0][fte]" max="50" min="0">
            </div>
            
            <div id="student_container"></div>
            
            <div class="form-entry">
                <button type="button" onclick="addStudent()">+ Add Another Student</button>
            </div>
            <div class="form-entry">
                <button type="submit" class="bottom">Next →</button>
            </div>
        </form>
    </div>
    <footer>
        <div class="footerText">
            <div class="name">
                <p>&copy; 2025 Malik Robinson, Ben Givens. All rights reserved.</p>
            </div>
            <div class="Links">
                <p><a href="footerpages/termsandcons.html">Terms and Conditions</a></p>
                <p><a href="footerpages/privacy.html">Privacy Policy</a></p>
                <p><a href="footerpages/cookie.html">Cookie Policy</a></p>
            </div>
        </div>
        </div>
    </footer>
</body>

</html>