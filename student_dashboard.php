<?php
session_start();
include 'db_config.php';

// Check if student is logged in
if (!isset($_SESSION['student'])) {
    header("Location: student_login.php");
    exit();
}

$student_email = $_SESSION['student']['email'];
$student_name = $_SESSION['student']['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Library Account | SmartX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans">

    <nav class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
        <div class="font-bold text-xl text-slate-800">JECRC <span class="text-red-600">SmartX</span></div>
        <div class="flex items-center gap-6">
            <a href="index.php" class="text-slate-600 hover:text-red-600">Browse Books</a>
            <a href="logout.php" class="text-red-500 font-bold">Logout</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Welcome, <?php echo $student_name; ?>!</h1>
        <p class="text-slate-500 mb-10">Manage your borrowed books and active reservations below.</p>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-book-reader text-red-600"></i> My Borrowed Books
                </h2>
            </div>
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black">
                    <tr>
                        <th class="p-4">Book Title</th>
                        <th class="p-4">Due Date</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    $sql = "SELECT b.title, t.due_date, t.status 
                            FROM transactions t 
                            JOIN books b ON t.book_id = b.id 
                            WHERE t.student_name = '$student_name' AND t.status = 'issued'";
                    $res = $conn->query($sql);
                    if ($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            echo "<tr>
                                <td class='p-4 font-bold text-slate-700'>{$row['title']}</td>
                                <td class='p-4 text-slate-500'>{$row['due_date']}</td>
                                <td class='p-4'><span class='text-orange-600 font-bold uppercase text-xs italic'>Current</span></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='p-8 text-center text-slate-400 italic'>You don't have any books currently.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden border-l-4 border-l-orange-500">
            <div class="p-6 border-b border-slate-50 bg-orange-50/30">
                <h2 class="font-bold text-orange-600 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left"></i> My Waitlist / Reservations
                </h2>
            </div>
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black">
                    <tr>
                        <th class="p-4">Book Title</th>
                        <th class="p-4">Requested On</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    $res_sql = "SELECT r.id, b.title, r.request_date 
                                FROM reservations r 
                                JOIN books b ON r.book_id = b.id 
                                WHERE r.student_email = '$student_email' AND r.status = 'pending'";
                    $res_result = $conn->query($res_sql);
                    if ($res_result->num_rows > 0) {
                        while($res_row = $res_result->fetch_assoc()) {
                            echo "<tr>
                                <td class='p-4 font-bold text-slate-700'>{$res_row['title']}</td>
                                <td class='p-4 text-slate-500'>{$res_row['request_date']}</td>
                                <td class='p-4 text-right'>
                                    <a href='cancel_reservation.php?id={$res_row['id']}' 
                                       class='text-red-500 hover:underline font-bold text-xs uppercase'
                                       onclick='return confirm(\"Cancel this reservation?\")'>Cancel</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='p-8 text-center text-slate-400 italic'>No active reservations.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>