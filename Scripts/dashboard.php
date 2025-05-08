<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit();
}

// Database configuration
$host = "localhost:3306";
$username = "report";
$password = "4U8s8w8e$";
$database = "users_report";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$errors = [];
$success = "";

// Handle form submission for posting an idea
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id']; // Get the logged-in user ID

    // Validate inputs
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    // Insert the idea into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO ideas (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $description);

        if ($stmt->execute()) {
            $success = "Your idea has been posted successfully!";
        } else {
            $errors[] = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all ideas from the database
$ideas = [];
$stmt = $conn->prepare("SELECT ideas.id, ideas.title, ideas.description, users.name FROM ideas INNER JOIN users ON ideas.user_id = users.id ORDER BY ideas.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $ideas[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="logo">Dashboard</a>
            <ul class="navbar-links">
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="logout-btn">Log Out</a></li>
            </ul>
            <!-- Hamburger Menu -->
            <div class="hamburger-menu" onclick="toggleNavbar()">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </div>
    </nav>

    <a href="#idea-form" class="sticky-button">+</a>

	
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="tagline">Post your ideas here</p>
        </header>

        <!-- Display posted ideas -->
        <section class="posted-ideas">
            <h2>Posted Ideas</h2>
            <?php if (!empty($ideas)): ?>
                <ul class="idea-list">
                    <?php foreach ($ideas as $idea): ?>
                        <li class="idea">
                            <h3><?php echo htmlspecialchars($idea['title']); ?></h3>
                            <p><strong>By:</strong> <?php echo htmlspecialchars($idea['name']); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($idea['description'])); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No ideas posted yet.</p>
            <?php endif; ?>
        </section>

        <!-- Form to post a new idea -->
        <section class="idea-form-container" id="idea-form">
            <form action="dashboard.php" method="POST" class="idea-form">
                <h2>Post Your Idea</h2>

                <?php if (!empty($errors)): ?>
                    <div class="error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        <p><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <label for="title">Idea Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <button type="submit">Post Idea</button>
            </form>
        </section>
    </div>

    <script>
        // Toggle the visibility of the navbar links on mobile
        function toggleNavbar() {
            const navbarLinks = document.querySelector('.navbar-links');
            navbarLinks.classList.toggle('active');
        }
    </script>
</body>
</html>
