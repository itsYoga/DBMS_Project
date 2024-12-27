<?php
// register.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Simple input validation
    if (!empty($username) && !empty($password) && !empty($email)) {
        // Insert user into the database
        $sql = "INSERT INTO users (username, password, student_id) VALUES ('$username', '$password', NULL)";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php"); // Redirect to login page
            exit;
        } else {
            echo "Error: " . $conn->error; // Show SQL error
        }
    } else {
        echo "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>