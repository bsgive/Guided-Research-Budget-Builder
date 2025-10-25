<?php
session_start();
include 'database.php'; // make sure this defines $conn = new mysqli(...)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ✅ Store all five years of effort
    $_SESSION['personnel'] = [
        'pi_id'     => $_POST['pi_id'],
        'effort_y1' => $_POST['effort_y1'],
        'effort_y2' => $_POST['effort_y2'],
        'effort_y3' => $_POST['effort_y3'],
        'effort_y4' => $_POST['effort_y4'],
        'effort_y5' => $_POST['effort_y5']
    ];

    // Redirect to next step
    header("Location: step3_students.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 2 - Personnel</title>
</head>
<body>
<h2>Step 2: Personnel Information</h2>
<form action="step2_personnel.php" method="POST">

    <label>Principal Investigator (PI):</label>
    <select name="pi_id" required>
        <option value="">-- Select PI --</option>
        <?php
        // Fetch faculty list
        $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
        while ($row = $faculty->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label>Effort (%) Year 1:</label>
    <input type="number" name="effort_y1" min="0" max="100" required>

    <label>Effort (%) Year 2:</label>
    <input type="number" name="effort_y2" min="0" max="100" required>

    <label>Effort (%) Year 3:</label>
    <input type="number" name="effort_y3" min="0" max="100" required>

    <label>Effort (%) Year 4:</label>
    <input type="number" name="effort_y4" min="0" max="100" required>

    <label>Effort (%) Year 5:</label>
    <input type="number" name="effort_y5" min="0" max="100" required>

    <br><br>
    <button type="submit">Next →</button>
</form>
</body>
</html>
