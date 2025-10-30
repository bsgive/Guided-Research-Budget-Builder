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
    <h2>Step 1: Project Plan</h2>
<div class="phpDoc1">
    <?php
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    ?>

    <form action="step1_plan.php" method="POST"  class="projectForm">

        <div class="form-group">

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
                    <select id="start_year" name="start_year" class="startYear"required>
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

            <div class = "rowInput">
                <div class ="form-entry_description">
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
</body>
</html>
