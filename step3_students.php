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
    <title>Step 3 - Student Support</title>
</head>
<body>
<h2>Step 3: Student Appointments</h2>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form action="step3_students.php" method="POST">
    <label>Student:</label>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php
        $students = $conn->query("SELECT id, name FROM students ORDER BY name");
        while ($row = $students->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label>FTE (max 50%):</label>
    <input type="number" name="fte" max="50" min="0" required>

    <br><br>
    <button type="submit">Next →</button>
</form>
</body>
</html>
