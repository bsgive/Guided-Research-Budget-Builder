<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userManagement.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

$stmt = $conn->prepare("
    SELECT id, title, start_year, duration_years, created_at
    FROM budgets
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Budgets - Research Budget Builder</title>
    <link rel="stylesheet" href="stylesheets.css?v=2" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap" rel="stylesheet">
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
            <a class="link" href="logout.php">Logout</a>
        </div>
    </div>

    <h2>Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
    
    <div class="phpDoc1" id="budgets">
        <div class="budget-header">
            <h2>My Budgets</h2>
            <a href="step1_plan.php" class="btn1">+ Create New Budget</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="budget-table">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Start Year</th>
                        <th>Duration</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= (int)$row['start_year'] ?></td>
                            <td><?= (int)$row['duration_years'] ?> year(s)</td>
                            <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                            <td class="budget-actions">
                                <a href="delete_budget.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Are you sure you want to delete this budget? This action cannot be undone.');" class="delete-link">Delete</a>
                                <a href="calculate.php?budget_id=<?= (int)$row['id'] ?>">Export</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="budget-empty">
                <p>You haven't created any budgets yet.</p>
                <a href="step1_plan.php" class="btn1">Create Your First Budget</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
