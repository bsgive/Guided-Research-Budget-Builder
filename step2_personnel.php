<?php
session_start();
include 'database.php'; 

// Make sure step1 data exists
if (!isset($_SESSION['plan'])) {
    header("Location: step1_plan.php");
    exit;
}

$duration = $_SESSION['plan']['duration']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['co_pis'])) {
        $_SESSION['personnel'] = $_POST['co_pis'];
    } else {
        $_SESSION['personnel'] = [];
    }

    header("Location: step3_students.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Step 2 — Co-Investigators</title>
<link rel="stylesheet" href="stylesheets.css" />
<script>
function addCoPI() {
    const container = document.getElementById('co_pi_container');
    const index = container.children.length;
    const duration = <?php echo $duration; ?>;
    
    let html = `<div class="co_pi_block">
        <label>Co-PI:</label>
        <select name="co_pis[${index}][id]" required>
            <option value="">-- Select Co-PI --</option>
            <?php
            $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
            while ($row = $faculty->fetch_assoc()) {
                echo "document.write('<option value=\"{$row['id']}\">{$row['name']}</option>');";
            }
            ?>
        </select>`;
    
    for (let i = 1; i <= duration; i++) {
        html += `<label>Effort Year ${i} (%)</label>
                 <input type="number" name="co_pis[${index}][effort_y${i}]" min="0" max="100" required>`;
    }
    
    html += `<button type="button" onclick="this.parentElement.remove()">Remove Co-PI</button>
    </div>`;
    
    container.insertAdjacentHTML('beforeend', html);
}
</script>
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

<h2>Step 2: Co-Investigators</h2>

<form method="POST" action="step2_personnel.php">
    <div id="co_pi_container"></div>

    <button type="button" onclick="addCoPI()">Add Co-PI</button>
    <br><br>
    <button type="submit">Next →</button>
</form>

</body>
</html>


