<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Invalid email format!'); window.location.href='login page.html';</script>");
    }

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'guardianqrcide');
    if ($conn->connect_error) {
        die("<script>alert('Database connection failed!'); window.location.href='login page.html';</script>");
    }

    // Fetch stored hash
    $stmt = $conn->prepare("SELECT password FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 0) {
        die("<script>alert('Email not registered!'); window.location.href='login page.html';</script>");
    }

    // Verify password
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        echo "<script>alert('Login successful!'); window.location.href='qrcode.html';</script>";
    } else {
        echo "<script>alert('Incorrect password!'); window.location.href='login page.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='login page.html';</script>";
}
?>