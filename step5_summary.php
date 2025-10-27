<?php
session_start();
include 'database.php';

if (isset($_POST['save'])) {

    $plan = $_SESSION['plan'];
    $stmt = $conn->prepare("INSERT INTO budgets (pi_id, title, start_year, duration_years) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $plan['pi_id'], $plan['title'], $plan['start_year'], $plan['duration']);
    $stmt->execute();
    $budget_id = $stmt->insert_id; 
    if (!empty($_SESSION['personnel'])) {
        $p = $_SESSION['personnel'];
        $stmt = $conn->prepare("INSERT INTO budget_personnel (budget_id, pi_id, effort_y1, effort_y2, effort_y3, effort_y4, effort_y5) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiiii", $budget_id, $p['pi_id'], $p['effort_y1'], $p['effort_y2'], $p['effort_y3'], $p['effort_y4'], $p['effort_y5']);
        $stmt->execute();
    }
    if (!empty($_SESSION['students'])) {
        foreach ($_SESSION['students'] as $s) {
            $stmt = $conn->prepare("INSERT INTO budget_students (budget_id, student_id, fte) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $budget_id, $s['student_id'], $s['fte']);
            $stmt->execute();
        }
    }
    if (!empty($_SESSION['travel'])) {
        foreach ($_SESSION['travel'] as $t) {
            $stmt = $conn->prepare("INSERT INTO budget_travel (budget_id, travel_type_id, trips, days) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $budget_id, $t['travel_type'], $t['trips'], $t['days']);
            $stmt->execute();
        }
    }

    echo "<p style='color:green;'>Budget saved successfully!</p>";
    session_unset();
}
?>
