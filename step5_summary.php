<?php
session_start();
include 'database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';


if (isset($_POST['save'])) {
    if (empty($_SESSION['plan'])) {
        $message = "<p style='color:red;'>Error: Missing project plan data.</p>";
    } else {
        $plan = $_SESSION['plan'];
        $duration = $plan['duration'];

        // --- Insert main budget record
        $stmt = $conn->prepare("INSERT INTO budgets (pi_id, title, start_year, duration_years) VALUES (?, ?, ?, ?)");
        if (!$stmt) die("Prepare failed (budget): " . $conn->error);
        $stmt->bind_param("isii", $plan['pi_id'], $plan['title'], $plan['start_year'], $duration);
        if (!$stmt->execute()) die("Budget insert error: " . $stmt->error);
        $budget_id = $stmt->insert_id;
        $stmt->close();

        // --- Insert personnel (PI and other personnel)
        if (!empty($_SESSION['personnel'])) {
            foreach ($_SESSION['personnel'] as $p) {

                if (empty($p['id'])) {
                    echo "<p style='color:red;'>Skipping personnel entry with missing ID.</p>";
                    continue;
                }

                $pi_id = (int)$p['id'];
                $effort_y1 = isset($p['effort_y1']) ? (int)$p['effort_y1'] : 0;
                $effort_y2 = isset($p['effort_y2']) ? (int)$p['effort_y2'] : 0;
                $effort_y3 = isset($p['effort_y3']) ? (int)$p['effort_y3'] : 0;
                $effort_y4 = isset($p['effort_y4']) ? (int)$p['effort_y4'] : 0;
                $effort_y5 = isset($p['effort_y5']) ? (int)$p['effort_y5'] : 0;

                $stmt = $conn->prepare(
                    "INSERT INTO budget_personnel (budget_id, pi_id, effort_y1, effort_y2, effort_y3, effort_y4, effort_y5)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                if (!$stmt) die("Prepare failed (personnel): " . $conn->error);

                $stmt->bind_param("iiiiiii", $budget_id, $pi_id, $effort_y1, $effort_y2, $effort_y3, $effort_y4, $effort_y5);
                if (!$stmt->execute()) die("Personnel insert error: " . $stmt->error);
                $stmt->close();
            }
        }

        // --- Insert students
        if (!empty($_SESSION['students'])) {
            $stmt = $conn->prepare("INSERT INTO budget_students (budget_id, student_id, fte) VALUES (?, ?, ?)");
            if (!$stmt) die("Prepare failed (students): " . $conn->error);

            foreach ($_SESSION['students'] as $s) {
                if (empty($s['student_id'])) continue;
                $student_id = (int)$s['student_id'];
                $fte = isset($s['fte']) ? (int)$s['fte'] : 0;

                $stmt->bind_param("iii", $budget_id, $student_id, $fte);
                if (!$stmt->execute()) die("Student insert error: " . $stmt->error);
            }
            $stmt->close();
        }

        // --- Insert travel
        if (!empty($_SESSION['travel'])) {
            $stmt = $conn->prepare("INSERT INTO budget_travel (budget_id, travel_type_id, trips, days) VALUES (?, ?, ?, ?)");
            if (!$stmt) die("Prepare failed (travel): " . $conn->error);

            foreach ($_SESSION['travel'] as $t) {
                $travel_type_id = isset($t['travel_type']) ? (int)$t['travel_type'] : 0;
                $trips = isset($t['trips']) ? (int)$t['trips'] : 0;
                $days = isset($t['days']) ? (int)$t['days'] : 0;

                $stmt->bind_param("iiii", $budget_id, $travel_type_id, $trips, $days);
                if (!$stmt->execute()) die("Travel insert error: " . $stmt->error);
            }
            $stmt->close();
        }

        $message = "<p style='color:green;'>✅ Budget saved successfully!</p>";
        session_unset();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Budget Summary</title>
<link rel="stylesheet" href="stylesheets.css" />
</head>
<body>
<div class="header">
    <div class="budgets">
        <img src="assets/logo.png" alt="Logo" class="Logo">
        <p class="budgetsText">Research Budget Builder</p>
    </div>
</div>

<h2>Step 5: Summary</h2>

<?php if($message) echo $message; ?>

<h3>Project Plan</h3>
<ul>
    <li>Title: <?= htmlspecialchars($_SESSION['plan']['title'] ?? '') ?></li>
    <li>PI: <?= htmlspecialchars($_SESSION['plan']['pi_id'] ?? '') ?></li>
    <li>Start Year: <?= htmlspecialchars($_SESSION['plan']['start_year'] ?? '') ?></li>
    <li>Duration: <?= htmlspecialchars($_SESSION['plan']['duration'] ?? '') ?> year(s)</li>
    <li>Description: <?= htmlspecialchars($_SESSION['plan']['description'] ?? '') ?></li>
</ul>

<h3>Personnel</h3>
<?php if(!empty($_SESSION['personnel'])): ?>
<ul>
<?php foreach($_SESSION['personnel'] as $p): ?>
    <li>
        <?= htmlspecialchars($p['name'] ?? 'Unnamed') ?> 
        - Efforts: 
        <?php
        for ($i = 1; $i <= ($_SESSION['plan']['duration'] ?? 0); $i++) {
            echo "Year $i: " . ($p["effort_y{$i}"] ?? 0) . "% ";
        }
        ?>
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h3>Students</h3>
<?php if(!empty($_SESSION['students'])): ?>
<ul>
<?php foreach($_SESSION['students'] as $s): ?>
    <li>Student ID <?= htmlspecialchars($s['student_id']) ?> - FTE: <?= htmlspecialchars($s['fte']) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h3>Travel</h3>
<?php if(!empty($_SESSION['travel'])): ?>
<ul>
<?php foreach($_SESSION['travel'] as $t): ?>
    <li>Type: <?= htmlspecialchars($t['travel_type']) ?>, Trips: <?= htmlspecialchars($t['trips']) ?>, Days: <?= htmlspecialchars($t['days']) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="step5_summary.php">
    <button type="submit" name="save">💾 Save Budget</button>
    <a href="index.html"><button type="button">🏠 Home</button></a>
</form>

</body>
</html>







