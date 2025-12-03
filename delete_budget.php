<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userManagement.php");
    exit;
}

$budget_id = $_GET['id'] ?? null;
if (!$budget_id) {
    $_SESSION['error'] = "Missing budget ID";
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
    $_SESSION['error'] = "Budget not found or you don't have permission to delete it.";
    header("Location: budgets.php");
    exit;
}

//Make sure to delete other tables first before main table
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
    $_SESSION['success'] = "Budget deleted successfully.";
} else {
    $_SESSION['error'] = "Error deleting budget.";
}
$stmt->close();

$conn->close();

header("Location: budgets.php");
exit;
?>
