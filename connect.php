<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : "";
    $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : "";
    $mobile = isset($_POST['mobile']) ? htmlspecialchars(trim($_POST['mobile'])) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : "";

    // Check if passwords match
    if ($password !== $password2) {
        echo "<script>alert('Error: Passwords do not match!'); window.location.href='sign_up.html';</script>";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Error: Invalid email format!'); window.location.href='sign_up.html';</script>";
        exit();
    }

    // Validate mobile number (must be 10-15 digits)
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "<script>alert('Error: Invalid mobile number!'); window.location.href='sign_up.html';</script>";
        exit();
    }

    // Function to verify email using Abstract API
    function verifyEmail($email) {
        $apiKey = "08ff7b5fd00b4940930dbf082264edd5"; // Replace with your Abstract API key
        $url = "https://emailvalidation.abstractapi.com/v1/?api_key=" . $apiKey . "&email=" . urlencode($email);

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        // Check if email is valid and deliverable
        return isset($data["is_valid_format"]["value"]) && $data["is_valid_format"]["value"] === true &&
               isset($data["deliverability"]) && $data["deliverability"] === "DELIVERABLE";
    }

    // Verify email with Abstract API before storing in DB
    if (!verifyEmail($email)) {
        echo "<script>alert('Error: Invalid or non-existent email!'); window.location.href='sign_up.html';</script>";
        exit();
    }

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Connect to MySQL database
    $conn = new mysqli('localhost', 'root', '', 'guardianqrcide');

    // Check database connection
    if ($conn->connect_error) {
        die("<script>alert('Database connection failed!'); window.location.href='login page.html';</script>");
    }

    // Check if email already exists in the database
    $check_email = $conn->prepare("SELECT email FROM registration WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Error: Email already registered!'); window.location.href='login page.html';</script>";
        $check_email->close();
        $conn->close();
        exit();
    }

    $check_email->close();

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO registration (fullname, address, mobile, password, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullname, $address, $mobile, $hashed_password, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login page.html';</script>";
    } else {
        echo "<script>alert('Error: Registration failed!'); window.location.href='sign_up.html';</script>";
    }

    // Close database connections
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='sign_up.html';</script>";
}
?>
