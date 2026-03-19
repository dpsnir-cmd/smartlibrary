<?php
session_start();
include 'db_config.php';

// 1. CHECK LOGIN: If they aren't logged in, send them to login page
if (!isset($_SESSION['student'])) {
    echo "<script>
        alert('You must login as a student to reserve books!'); 
        window.location.href='student_login.php';
    </script>";
    exit();
}

// 2. PROCESS RESERVATION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $book_id = $_POST['book_id'];
    $email = $_SESSION['student']['email']; // Get email from the logged-in session
    
    // A. Check if they already reserved it (Prevent duplicates)
    $check_sql = "SELECT * FROM reservations WHERE book_id = $book_id AND student_email = '$email' AND status = 'pending'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Already reserved
        echo "<script>
            alert('You have already reserved this book! Check your dashboard.'); 
            window.location.href='student_dashboard.php';
        </script>";
    } else {
        // B. Save to Database
        $insert_sql = "INSERT INTO reservations (book_id, student_email, request_date) 
                       VALUES ($book_id, '$email', CURDATE())";
        
        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>
                alert('Success! You are on the waitlist.'); 
                window.location.href='student_dashboard.php';
            </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
} else {
    // If someone tries to open this file directly without clicking the button
    header("Location: index.php");
}
?>