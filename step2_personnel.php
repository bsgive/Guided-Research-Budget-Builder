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
    $_SESSION['personnel'] = [];
    if (!empty($_POST['pi_id'])) {
        $pi_id = (int) $_POST['pi_id'];
        $stmt = $conn->prepare("SELECT name FROM faculty_staff WHERE id = ?");
        $stmt->bind_param("i", $pi_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pi_name = $result->fetch_assoc()['name'] ?? 'Unnamed';
        $stmt->close();

        $_SESSION['personnel'][] = [
            'id' => $pi_id,
            'name' => $pi_name,
            'effort_y1' => $_POST['effort_y1'] ?? 0,
            'effort_y2' => $_POST['effort_y2'] ?? 0,
            'effort_y3' => $_POST['effort_y3'] ?? 0,
            'effort_y4' => $_POST['effort_y4'] ?? 0,
            'effort_y5' => $_POST['effort_y5'] ?? 0,
        ];
        $_SESSION['plan']['pi_id'] = $_POST['pi_id'];
    }
    if (!empty($_POST['co_pis']) && is_array($_POST['co_pis'])) {
        foreach ($_POST['co_pis'] as $c) {
            if (empty($c['id']))
                continue;
            $co_id = (int) $c['id'];
            $stmt = $conn->prepare("SELECT name FROM faculty_staff WHERE id = ?");
            $stmt->bind_param("i", $co_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $co_name = $res->fetch_assoc()['name'] ?? 'Unnamed';
            $stmt->close();

            $_SESSION['personnel'][] = [
                'id' => $co_id,
                'name' => $co_name,
                'effort_y1' => $c['effort_y1'] ?? 0,
                'effort_y2' => $c['effort_y2'] ?? 0,
                'effort_y3' => $c['effort_y3'] ?? 0,
                'effort_y4' => $c['effort_y4'] ?? 0,
                'effort_y5' => $c['effort_y5'] ?? 0,
            ];
        }
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
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap"
        rel="stylesheet">
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
    <h2>Step 2: Personnel Information</h2>
    <form action="step2_personnel.php" method="POST">
        <div class="phpDoc1">
            <div class="form-group">
                <h2>Step 2: Personnel Information</h2>
                <label>Principal Investigator (PI):</label>
                <select name="pi_id" required>
                    <option value="">-- Select PI --</option>
                    <?php
                    $faculty = $conn->query("SELECT id, name FROM faculty_staff ORDER BY name");
                    while ($row = $faculty->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>

                <div class="smallInputs">
                    <?php for ($i = 1; $i <= $duration; $i++): ?>
                        <div class="form-entry">
                            <label>Effort (%) Year <?= $i ?>:</label>
                            <input type="number" name="effort_y<?= $i ?>" min="0" max="100" required>
                        </div>
                    <?php endfor; ?>
                </div>

                <h2>Step 2: Co-Investigators</h2>
                <div class="form-group" id="co_pi_container"></div>
                <button type="button" onclick="addCoPI()">Add Co-PI</button>

                <div>
                    <button type="submit" class="bottom">Next →</button>
                </div>
            </div>
        </div>
    </form>

</body>

</html>