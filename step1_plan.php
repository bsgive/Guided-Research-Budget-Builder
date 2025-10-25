<?php
session_start();
include 'database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    
    if (!empty($_POST['title']) && !empty($_POST['start_year']) && !empty($_POST['duration'])) {

    
        $_SESSION['plan'] = [
            'title'       => $_POST['title'],
            'pi_id'       => $_POST['pi_id'],
            'start_year'  => $_POST['start_year'],
            'duration'    => $_POST['duration'],
            'description' => $_POST['description']
        ];

        header("Location: step2_personnel.php");
        exit;
    } else {
        $error = "Please fill out all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 1 - Project Plan</title>
</head>
<body>
<h2>Step 1: Project Plan</h2>

<?php
if (isset($error)) {
    echo "<p class='error'>$error</p>";
}
?>

<form action="step1_plan.php" method="POST">

    <label for="title">Project Title:</label>
    <input type="text" id="title" name="title" required>

    <label for="pi_id">Principal Investigator (PI):</label>
    <select id="pi_id" name="pi_id" required>
        <option value="">-- Select PI --</option>
        <?php
        $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
        while ($row = $faculty->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label for="start_year">Start Year:</label>
    <select id="start_year" name="start_year" required>
        <?php
        $current_year = date("Y");
        for ($i = 0; $i < 5; $i++) {
            $year = $current_year + $i;
            echo "<option value='$year'>$year</option>";
        }
        ?>
    </select>

    <label for="duration">Project Duration (Years):</label>
    <select id="duration" name="duration" required>
        <?php
        for ($i = 1; $i <= 5; $i++) {
            echo "<option value='$i'>$i year(s)</option>";
        }
        ?>
    </select>

    <label for="description">Project Description / Notes (optional):</label>
    <textarea id="description" name="description" rows="4" cols="40"></textarea>

    <br><br>
    <button type="submit">Next →</button>
</form>

</body>
</html>
