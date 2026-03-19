<?php
include 'db_config.php';

if (isset($_GET['transaction_id'])) {
    $t_id = $_GET['transaction_id'];

    // 1. Find which book is being returned
    $t_data = $conn->query("SELECT book_id FROM transactions WHERE id = $t_id")->fetch_assoc();
    $book_id = $t_data['book_id'];

    // 2. Update Transaction Status
    $conn->query("UPDATE transactions SET status = 'returned', return_date = CURRENT_DATE WHERE id = $t_id");

    // 3. Smart Inventory Update: Add 1 back to stock
    $conn->query("UPDATE books SET available_qty = available_qty + 1 WHERE id = $book_id");

    echo "<script>alert('Book Returned Successfully!'); window.location.href='admin_dashboard.php';</script>";
}
?>