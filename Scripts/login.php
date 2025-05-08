<?php
$host = "";
$username = "";
$password = "";
$database = "";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;

                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Incorrect email or password";
            }
        } else {
            $errors[] = "No account found with this email";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="./Styles/stylesAction.css">
</head>
<body>
    <div class="form-container">
        <h1>Log In</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">

                    <?php foreach ($errors as $error): ?>
                        <?php echo htmlspecialchars($error); ?>
                    <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <input type="email" id="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
        
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>
</html>

