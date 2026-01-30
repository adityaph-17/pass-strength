<?php
session_start();

$password = "";
$isSubmitted = false;
$score = 0;
$strengthText = "";
$strengthClass = "";
$suggestions = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["password"])) {

    $password = $_POST["password"];

    // Length check
    if (strlen($password) >= 8) $score++;
    else $suggestions[] = "Use at least 8 characters";

    if (strlen($password) >= 12) $score++; // extra point for longer length

    // Character checks
    if (preg_match('/[A-Z]/', $password)) $score++;
    else $suggestions[] = "Add uppercase letters (A–Z)";

    if (preg_match('/[a-z]/', $password)) $score++;
    else $suggestions[] = "Add lowercase letters (a–z)";

    if (preg_match('/[0-9]/', $password)) $score++;
    else $suggestions[] = "Include numbers (0–9)";

    if (preg_match('/[\W]/', $password)) $score++;
    else $suggestions[] = "Add special characters (@, #, %, &)";

    // Strength mapping (0–6)
    if ($score <= 1) {
        $strengthText = "Very Weak";
        $strengthClass = "very-weak";
    } elseif ($score == 2) {
        $strengthText = "Weak";
        $strengthClass = "weak";
    } elseif ($score == 3 || $score == 4) {
        $strengthText = "Medium";
        $strengthClass = "medium";
    } elseif ($score == 5) {
        $strengthText = "Strong";
        $strengthClass = "strong";
    } else {
        $strengthText = "Very Strong";
        $strengthClass = "very-strong";
    }

    // Save result in session
    $_SESSION['password_result'] = [
        'strengthText' => $strengthText,
        'strengthClass' => $strengthClass,
        'suggestions' => $suggestions
    ];

    // Redirect to same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if result exists in session
if (isset($_SESSION['password_result'])) {
    $isSubmitted = true;
    $strengthText = $_SESSION['password_result']['strengthText'];
    $strengthClass = $_SESSION['password_result']['strengthClass'];
    $suggestions = $_SESSION['password_result']['suggestions'];

    // Clear session after showing once
    unset($_SESSION['password_result']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Strength Checker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- HEADER -->
    <header class="header">
        <a href="#" class="logo">🔐 SecurePass</a>
        <span class="user-tag">Security Tool</span>
    </header>

    <main class="container">

        <!-- HERO SECTION -->
        <section class="hero-section">
            <h1>Password Strength Checker</h1>
            <p>Analyze password security, detect weaknesses, and follow best practices to stay protected.</p>
        </section>

        <!-- PASSWORD CHECK CARD -->
        <section class="section-header">
            <h2>Check Password Strength</h2>
        </section>

        <section class="search-card">
            <form method="POST">
                <input type="password" name="password" placeholder="Enter your password" required>
                <button class="btn">Check Strength 🛡️</button>
            </form>

            <p class="info-text">Passwords are processed securely and never stored.</p><br>

            <section class="search-card">

                <section>
                    <h4>RESULT :-</h4>
                </section>

                <div class="strength-bar">
    <div class="strength-fill <?= $strengthClass ?>"></div>
</div>

                <!-- RESULT -->
                <div id="result-container" <?php if (!$isSubmitted) echo 'style="display:none;"'; ?>>
                    <p class="info-text <?= $strengthClass ?>">
                        🔐 Password Strength: <b><?= $strengthText ?></b>
                    </p>

                    <div class="strength-bar">
                        <div class="strength-fill <?= $strengthClass ?>"></div>
                    </div>

                    <?php if (!empty($suggestions)): ?>
                        <ul class="info-text">
                            <?php echo "<b>Suggestions to improve your password:</b>"; ?>

                            <?php foreach ($suggestions as $tip): ?>

                                <b>
                                    <li>⚠️ <?= $tip ?></li>
                                </b>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </section>
        </section>

        <!-- STRENGTH LEVELS -->
        <section class="section-header">
            <h2>Password Strength Levels</h2>
        </section>

        <section class="search-card">
            <table class="strength-table">
                <thead>
                    <tr>
                        <th>Password Example</th>
                        <th>Estimated Time to Crack</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="very-weak">123456</td>
                        <td>Seconds to Minutes</td>
                    </tr>
                    <tr>
                        <td class="weak">password123</td>
                        <td>Hours to Weeks</td>
                    </tr>
                    <tr>
                        <td class="medium">Hello@123</td>
                        <td>Months to Years</td>
                    </tr>
                    <tr>
                        <td class="strong">HappyLife@2025</td>
                        <td>Decades</td>
                    </tr>
                    <tr>
                        <td class="very-strong">T9$kL!8zQ@21</td>
                        <td>Centuries</td>
                    </tr>
                </tbody>
            </table>
        </section>
        <!-- BEST PRACTICES -->
        <section class="section-header">
            <h2>Password Best Practices</h2>
        </section>

        <section class="best-practices">

            <div class="practice-card" onclick="toggleCard(this)">
                <h3>Password Length</h3>
                <p class="practice-info">
                    A strong password should be at least 8–12 characters long.
                    Longer passwords significantly increase resistance to brute-force attacks.
                    <a href="https://en.wikipedia.org/wiki/Password_strength" target="_blank">Wikipedia</a>
                </p>
            </div>

            <div class="practice-card" onclick="toggleCard(this)">
                <h3>Character Complexity</h3>
                <p class="practice-info">
                    Use a mix of uppercase letters, lowercase letters, numbers, and special symbols.
                    This increases entropy and makes guessing more difficult.
                    <a href="https://en.wikipedia.org/wiki/Password_policy" target="_blank">Wikipedia</a>
                </p>
            </div>

            <div class="practice-card" onclick="toggleCard(this)">
                <h3>Avoid Personal Information</h3>
                <p class="practice-info">
                    Avoid using names, dates of birth, phone numbers, or common words.
                    Such information can be easily obtained through social engineering.
                    <a href="https://en.wikipedia.org/wiki/Social_engineering_(security)" target="_blank">Wikipedia</a>
                </p>
            </div>

            <div class="practice-card" onclick="toggleCard(this)">
                <h3>Unique Passwords</h3>
                <p class="practice-info">
                    Each account should have a unique password.
                    Reusing passwords increases risk if one service is compromised.
                    <a href="https://en.wikipedia.org/wiki/Credential_stuffing" target="_blank">Wikipedia</a>
                </p>
            </div>

        </section>

        <!-- RISKS -->
        <section class="section-header">
            <h2>Risks of Weak Passwords</h2>
        </section>

        <section class="risk-list">

            <div class="practice-card risk-card" onclick="toggleCard(this)">
                <h3>Why are weak passwords risky?
                    <span class="arrow">▼</span>
                </h3>
                <p class="practice-info">
                    Weak passwords are easy to guess or crack using automated tools, allowing attackers to gain unauthorized access.
                    <a href="https://en.wikipedia.org/wiki/Password_strength" target="_blank">Wikipedia</a>
                </p>
            </div>
            <br>
            <div class="practice-card risk-card" onclick="toggleCard(this)">
                <h3>What is a brute-force attack?
                    <span class="arrow">▼</span>
                </h3>
                <p class="practice-info">
                    A brute-force attack tries every possible combination until the correct password is found.
                    <a href="https://en.wikipedia.org/wiki/Brute-force_attack" target="_blank">Wikipedia</a>
                </p>
            </div>
            <br>

            <div class="practice-card risk-card" onclick="toggleCard(this)">
                <h3>What is a dictionary attack?
                    <span class="arrow">▼</span>
                </h3>
                <p class="practice-info">
                    A dictionary attack uses lists of common passwords and words to quickly guess weak credentials.
                    <a href="https://en.wikipedia.org/wiki/Dictionary_attack" target="_blank">Wikipedia</a>
                </p>
            </div>
            <br>

            <div class="practice-card risk-card" onclick="toggleCard(this)">
                <h3>Can weak passwords cause data breaches?
                    <span class="arrow">▼</span>
                </h3>
                <p class="practice-info">
                    Yes, weak passwords are one of the leading causes of data breaches worldwide.
                    <a href="https://en.wikipedia.org/wiki/Data_breach" target="_blank">Wikipedia</a>
                </p>
            </div>
            <br>

            <div class="practice-card risk-card" onclick="toggleCard(this)">
                <h3>How can users stay safe?
                    <span class="arrow">▼</span>
                </h3>
                <p class="practice-info">
                    Users can stay safe by using strong, unique passwords and enabling additional security measures.
                    <a href="https://en.wikipedia.org/wiki/Computer_security" target="_blank">Wikipedia</a>
                </p>
            </div>

        </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">

            <p>Password Security Awareness Tool </p>
        </div>
        <p class="info-text">Made with ❤️ by SecurePass Team (Aditya Halne)</p>
    </footer>

    <script>
        function toggleCard(card) {
            document.querySelectorAll('.practice-card').forEach(c => {
                if (c !== card) c.classList.remove('active');
            });
            card.classList.toggle('active');
        }
    </script>

    <script>
        const passwordInput = document.querySelector('input[name="password"]');
        const resultContainer = document.getElementById('result-container');

        passwordInput.addEventListener('input', () => {
            // Hide the previous result when user starts typing a new password
            resultContainer.style.display = 'none';
        });
    </script>
</body>

</html>