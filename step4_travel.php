<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['travel_type']) && isset($_POST['trips']) && isset($_POST['days'])) {

        $_SESSION['travel'] = [
            [
                'travel_type' => $_POST['travel_type'],
                'trips' => (int) $_POST['trips'],
                'days' => (int) $_POST['days']
            ]
        ];

        header("Location: step5_summary.php");
        exit;

    } else {

        $_SESSION['travel'] = [];
        header("Location: step5_summary.php");
        exit;
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
                <label for="travel_type">Destination Type:</label>
                <select name="travel_type" id="travel_type">
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
                    <label for="trips">Number of Trips:</label>
                    <input type="number" id="trips" name="trips" min="0">
                </div>
                <div class="form-entry">
                    <label for="days">Duration (days per trip):</label>
                    <input type="number" id="days" name="days" min="1">
                </div>
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