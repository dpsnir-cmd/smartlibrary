<?php
session_start();
include 'db_config.php';

// 1. Security: Kick out if not Admin (Optional, uncomment if needed)
// if (!isset($_SESSION['admin'])) {
//     header("Location: admin_login.php");
//     exit();
// }

// 2. LOGIC: Delete a Reservation (When a book is given or request cancelled)
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $conn->query("DELETE FROM reservations WHERE id=$id");
    header("Location: manage_reservations.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Reservations | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold text-center border-b border-slate-800">
            Library<span class="text-red-600">X</span>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl text-slate-300 transition-colors">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="manage_reservations.php" class="flex items-center gap-3 p-3 bg-red-600 text-white rounded-xl shadow-lg shadow-red-900/50">
                <i class="fa-solid fa-clipboard-list"></i> Reservations
            </a>
            <a href="index.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-xl text-slate-300 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Home
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Waitlist Management</h1>
            <div class="bg-white px-4 py-2 rounded-lg shadow text-sm font-bold text-slate-500">
                Total Waiting: 
                <span class="text-red-600"><?php echo $conn->query("SELECT COUNT(*) as c FROM reservations")->fetch_assoc()['c']; ?></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                    <tr>
                        <th class="p-4">Student Email</th>
                        <th class="p-4">Book Requested</th>
                        <th class="p-4">Date Added</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    // Join reservations with books to get the Title
                    $sql = "SELECT r.id, r.student_email, r.request_date, b.title, b.available_qty 
                            FROM reservations r 
                            JOIN books b ON r.book_id = b.id 
                            ORDER BY r.request_date ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Check if book is back in stock
                            $inStock = ($row['available_qty'] > 0);
                            
                            echo "<tr class='hover:bg-slate-50 transition-colors'>
                                <td class='p-4 font-bold text-slate-700'>{$row['student_email']}</td>
                                <td class='p-4'>{$row['title']}</td>
                                <td class='p-4 text-sm text-slate-500'>{$row['request_date']}</td>
                                <td class='p-4'>";
                                
                            if ($inStock) {
                                echo "<span class='bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold uppercase'>In Stock Now!</span>";
                            } else {
                                echo "<span class='bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold uppercase'>Waiting</span>";
                            }

                            echo "</td>
                                <td class='p-4'>
                                    <a href='?del={$row['id']}' class='text-red-500 hover:text-red-700 font-bold text-xs uppercase' onclick='return confirm(\"Remove this reservation?\")'>
                                        <i class='fa-solid fa-trash'></i> Remove
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='p-10 text-center text-slate-400'>No active reservations found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>