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
    <h2>Step 2: Personnel Information</h2>
    <div class="phpDoc1">
        <div class="form-group">
            <div>
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
            </div>
            <div class="smallInputs">
                <div class = "form-entry">
                    <label>Effort (%) Year 1:</label>
                    <input type="number" name="effort_y1" min="0" max="100" required>
                </div>
                <div class = "form-entry">
                    <label>Effort (%) Year 2:</label>
                    <input type="number" name="effort_y2" min="0" max="100" required>
                </div>
                <div class = "form-entry">
                    <label>Effort (%) Year 3:</label>
                    <input type="number" name="effort_y3" min="0" max="100" required>
                </div>
                <div class = "form-entry">
                    <label>Effort (%) Year 4:</label>
                    <input type="number" name="effort_y4" min="0" max="100" required>
                </div>
                <div class = "form-entry">
                    <label>Effort (%) Year 5:</label>
                    <input type="number" name="effort_y5" min="0" max="100" required>
                </div>
            </div>
            <div>
                <button type="submit" class="bottom">Next →</button>
            </div>
        </div>
    </div>
            <h2>Step 2: Co-Investigators</h2>
    <div class="phpdoc1">
        <form method="POST" action="step2_personnel.php" class="form-group">
            <div class ="form-group" id="co_pi_container"></div>
            <button type="button" onclick="addCoPI()">Add Co-PI</button>
            <button type="submit" class="bottom">Next →</button>
        </form>
    </div>
</body>
</html>


