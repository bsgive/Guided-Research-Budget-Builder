<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userManagement.php");
    exit;
}

$budget_id = $_GET['id'] ?? null;
if (!$budget_id) {
    $_SESSION['error'] = "No budget ID provided";
    header("Location: budgets.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM budgets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $budget_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$budget = $result->fetch_assoc();
$stmt->close();

if (!$budget) {
    $_SESSION['error'] = "Couldn't find that budget or it's not yours";
    header("Location: budgets.php");
    exit;
}


$stmt = $conn->prepare("DELETE FROM budget_travel WHERE budget_id = ?");
$stmt->bind_param("i", $budget_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM budget_students WHERE budget_id = ?");
$stmt->bind_param("i", $budget_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM budget_personnel WHERE budget_id = ?");
$stmt->bind_param("i", $budget_id);
$stmt->execute();
$stmt->close();


$stmt = $conn->prepare("DELETE FROM budgets WHERE id = ?");
$stmt->bind_param("i", $budget_id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Budget deleted!";
} else {
    $_SESSION['error'] = "Couldn't delete the budget";
}
$stmt->close();

$conn->close();

header("Location: budgets.php");
exit;
?>
