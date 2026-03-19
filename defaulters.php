<?php
session_start();
include 'db_config.php';

// Define the fine rate (e.g., 10 per day)
$fine_rate = 10;

// Query to get only ISSUED books that are past the DUE DATE
$sql = "SELECT t.id, t.student_name, b.title, t.issue_date, t.due_date 
        FROM transactions t 
        JOIN books b ON t.book_id = b.id 
        WHERE t.status = 'issued' AND t.due_date < CURDATE()
        ORDER BY t.due_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Late Returns & Fines | SmartX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <a href="admin_dashboard.php" class="text-slate-400 hover:text-red-600 text-sm font-bold uppercase tracking-widest mb-2 block transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Defaulters <span class="text-red-600">& Fines</span></h1>
            </div>
            <div class="bg-white px-6 py-4 rounded-3xl shadow-sm border border-slate-200 text-right">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Active Defaulters</p>
                <p class="text-2xl font-black text-red-600"><?php echo $result->num_rows; ?></p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-900 text-white text-[10px] uppercase font-black tracking-[0.2em]">
                    <tr>
                        <th class="p-6">Student Name</th>
                        <th class="p-6">Book Title</th>
                        <th class="p-6">Due Date</th>
                        <th class="p-6">Days Late</th>
                        <th class="p-6">Current Fine</th>
                        <th class="p-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Calculate Days Late
                            $due = new DateTime($row['due_date']);
                            $today = new DateTime();
                            $diff = $today->diff($due);
                            $days_late = $diff->days;
                            $total_fine = $days_late * $fine_rate;
                    ?>
                    <tr class="hover:bg-red-50/30 transition-colors">
                        <td class="p-6 font-bold text-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <?php echo $row['student_name']; ?>
                            </div>
                        </td>
                        <td class="p-6 text-slate-600 italic font-medium">"<?php echo $row['title']; ?>"</td>
                        <td class="p-6 text-slate-500 font-bold"><?php echo $row['due_date']; ?></td>
                        <td class="p-6">
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-black">
                                <?php echo $days_late; ?> DAYS
                            </span>
                        </td>
                        <td class="p-6 text-xl font-black text-slate-900">
                            ₹<?php echo $total_fine; ?>
                        </td>
                        <td class="p-6 text-right">
                            <a href="manage_issued_books.php?return_id=<?php echo $row['id']; ?>" 
                               class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all shadow-lg">
                               Collect & Return
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='p-20 text-center'>
                                <div class='text-emerald-500 text-5xl mb-4'><i class='fa-solid fa-circle-check'></i></div>
                                <h3 class='text-xl font-bold text-slate-800'>No Defaulters!</h3>
                                <p class='text-slate-400'>All books are either returned or within their due dates.</p>
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>