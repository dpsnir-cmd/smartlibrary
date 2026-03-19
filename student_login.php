<?php
session_start();
include 'db_config.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM students WHERE email='$email' AND password='$password'");
    
    if ($result->num_rows > 0) {
        $_SESSION['student'] = $result->fetch_assoc();
        header("Location: student_dashboard.php");
    } else {
        echo "<script>alert('Invalid Credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center h-screen">
    <form method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-slate-800">Student Login</h2>
        <input type="email" name="email" placeholder="Email" class="w-full p-3 mb-4 border rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-3 mb-6 border rounded" required>
        <button type="submit" name="login" class="w-full bg-blue-600 text-white p-3 rounded font-bold hover:bg-blue-700">Login</button>
    </form>
</body>
</html>