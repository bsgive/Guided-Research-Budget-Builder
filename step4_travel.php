<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['travel'] = [];
    
    if (!empty($_POST['travel']) && is_array($_POST['travel'])) {
        foreach ($_POST['travel'] as $t) {
            if (empty($t['travel_type'])) {
                continue;
            }
            
            $trips = !empty($t['trips']) ? (int) $t['trips'] : 0;
            $days = isset($t['days']) ? (int) $t['days'] : 0;
            
            $travel_type_id = (int) $t['travel_type'];
            $stmt = $conn->prepare("SELECT type FROM travel_profiles WHERE id = ?");
            $stmt->bind_param("i", $travel_type_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $travel_row = $result->fetch_assoc();
            $travel_type_name = $travel_row ? $travel_row['type'] : 'Unknown';
            $stmt->close();
            
            $_SESSION['travel'][] = [
                'travel_type' => $travel_type_id,
                'travel_type_name' => $travel_type_name,
                'trips' => $trips,
                'days' => $days
            ];
        }
    }
    
    header("Location: step5_summary.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <script>
        function addTravel() {
            const container = document.getElementById('travel_container');
            const index = container.children.length + 1;

            let html = `<div class="co_pi_block">
        <label>Destination Type:</label>
        <select name="travel[${index}][travel_type]" required>
            <option value="">-- Select Travel Type --</option>
            <?php
            $profiles_js = $conn->query("SELECT id, type FROM travel_profiles ORDER BY type");
            while ($row = $profiles_js->fetch_assoc()) {
                echo "document.write('<option value=\"{$row['id']}\">{$row['type']}</option>');";}
            ?>
        </select>
        <div class="smallInputs">
            <div>
                <label>Number of Trips:</label>
                <input type="number" name="travel[${index}][trips]" min="0" required>
            </div>
            <div>
                <label>Days per trip:</label>
                <input type="number" name="travel[${index}][days]" min="1" required>
            </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()">Remove Travel</button>
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

    <h2>Step 4: Travel Planning</h2>

    <?php
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    ?>
    <div class="phpDoc1">
        <form action="step4_travel.php" method="POST" class="form-group">
            <div class="form-entry">
                <label>Destination Type:</label>
                <select name="travel[0][travel_type]">
                    <option value="">-- Select Travel Type --</option>
                    <?php
                    $profiles = $conn->query("SELECT id, type FROM travel_profiles ORDER BY type");
                    while ($row = $profiles->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['type']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="smallInputs">
                <div class="form-entry">
                    <label>Number of Trips:</label>
                    <input type="number" name="travel[0][trips]" min="0">
                </div>
                <div class="form-entry">
                    <label>Duration (days per trip):</label>
                    <input type="number" name="travel[0][days]" min="1">
                </div>
            </div>
            
            <div id="travel_container"></div>
            
            <div class="form-entry">
                <button type="button" onclick="addTravel()">+ Add Another Trip</button>
            </div>
            <div class="form-entry">
                <button type="submit">Next →</button>
            </div>
        </form>
    </div>
    <footer>
        <div class="footerText">
            <div class="name">
                <p>&copy; 2025 Malik Robinson, Ben Givens. All rights reserved.</p>
            </div>
            <div class="Links">
                <p><a href="footerpages/termsandcons.html">Terms and Conditions</a></p>
                <p><a href="footerpages/privacy.html">Privacy Policy</a></p>
                <p><a href="footerpages/cookie.html">Cookie Policy</a></p>
            </div>
        </div>
        </div>
    </footer>
</body>

</html>