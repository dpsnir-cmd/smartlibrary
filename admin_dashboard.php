<?php
session_start();
include 'db_config.php';

// --- SECURITY CHECK (Uncomment this later to secure the page) ---
// if (!isset($_SESSION['admin'])) {
//     header("Location: admin_login.php");
//     exit();
// }

// 1. Handle "Return Book" Action
if (isset($_GET['return'])) {
    $id = $_GET['return'];
    // Update Transaction to 'returned'
    $conn->query("UPDATE transactions SET status='returned', return_date=CURRENT_DATE WHERE id=$id");
    
    // Get Book ID to add stock back
    $t = $conn->query("SELECT book_id FROM transactions WHERE id=$id")->fetch_assoc();
    $bid = $t['book_id'];
    $conn->query("UPDATE books SET available_qty = available_qty + 1 WHERE id=$bid");
    
    header("Location: admin_dashboard.php");
}

// 2. Handle "Delete Book" Action
if (isset($_GET['delete_book'])) {
    $id = $_GET['delete_book'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: admin_dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Librarian Dashboard | SmartX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#f8fafc] flex h-screen p-4 gap-4 overflow-hidden">

  <aside class="w-72 bg-slate-900 rounded-[2rem] shadow-2xl flex flex-col overflow-hidden border border-slate-800">
    <div class="p-8 border-b border-slate-800/50">
        <h1 class="text-white text-2xl font-black italic tracking-tighter">
            SMART<span class="text-red-500">X</span> <span class="text-[10px] not-italic text-slate-500 block tracking-[0.3em] uppercase">Control Center</span>
        </h1>
    </div>
    
    <nav class="flex-1 p-6 space-y-3">
        <a href="admin_dashboard.php" class="flex items-center gap-4 p-4 bg-gradient-to-r from-red-600 to-red-500 rounded-2xl text-white shadow-lg shadow-red-500/40 font-bold transition-all hover:scale-[1.02]">
            <i class="fa-solid fa-chart-pie"></i> Overview
        </a>
        <a href="issue_book.php" class="flex items-center gap-4 p-4 text-emerald-500 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 font-bold transition-all hover:bg-emerald-500 hover:text-white group">
            <i class="fa-solid fa-plus-circle"></i>
             <span>Quick Issue Book</span>
            <i class="fa-solid fa-bolt ml-auto animate-pulse"></i>
        </a>
        
        <a href="manage_reservations.php" class="flex items-center gap-4 p-4 text-slate-400 hover:text-white hover:bg-slate-800 rounded-2xl transition-all group">
            <i class="fa-solid fa-bell group-hover:animate-swing"></i> Reservations
            <?php 
                $count = $conn->query("SELECT COUNT(*) as c FROM reservations")->fetch_assoc()['c'];
                if($count > 0) echo "<span class='ml-auto bg-red-500/10 text-red-500 text-[10px] px-2 py-0.5 rounded-full border border-red-500/20'>$count</span>";
            ?>
        </a>
        <a href="manage_issued_books.php" class="flex items-center gap-4 p-4 text-slate-400 hover:text-white hover:bg-slate-800 rounded-2xl transition-all">
            <i class="fa-solid fa-book-open-reader"></i>
              <span>Track Issued Books</span>
        </a>
        <a href="add_book.php" class="flex items-center gap-4 p-4 text-slate-400 hover:text-white hover:bg-slate-800 rounded-2xl transition-all">
            <i class="fa-solid fa-plus-circle"></i> Add New Book
        </a>

        <a href="manage_inventory.php" class="flex items-center gap-4 p-4 text-slate-400 hover:text-white hover:bg-slate-800 rounded-2xl transition-all">
            <i class="fa-solid fa-layer-group"></i> Inventory
        </a>
        <a href="defaulters.php" class="flex items-center gap-4 p-4 text-red-500 bg-red-500/10 rounded-2xl border border-red-500/20 font-bold transition-all hover:bg-red-600 hover:text-white group mt-4">
             <i class="fa-solid fa-triangle-exclamation"></i>
               <span>Fine & Defaulters</span>
                                  <?php 
                      $late_count = $conn->query("SELECT COUNT(*) as c FROM transactions WHERE status='issued' AND due_date < CURDATE()")->fetch_assoc()['c'];
                       if($late_count > 0) echo "<span class='ml-auto bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full'>$late_count</span>";
                         ?>
        </a>
    </nav>

    <div class="p-6 bg-slate-800/30 mt-auto border-t border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-gradient-to-tr from-slate-700 to-slate-600 rounded-full flex items-center justify-center text-white font-bold border border-slate-500 shadow-inner">
                A
            </div>
            <div>
                <p class="text-xs font-black text-white uppercase tracking-widest">Administrator</p>
                <a href="logout.php" class="text-[10px] text-red-400 hover:text-red-300 transition-colors uppercase font-bold">Sign Out</a>
            </div>
        </div>
    </div>
</aside>

    <main class="flex-1 overflow-y-auto p-8">
        
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Dashboard Overview</h1>
            <div class="text-sm text-slate-500">Welcome back, Librarian</div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Total Books</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1">
                            <?php echo $conn->query("SELECT SUM(quantity) as total FROM books")->fetch_assoc()['total']; ?>
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Currently Issued</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1">
                            <?php echo $conn->query("SELECT COUNT(*) as c FROM transactions WHERE status='issued'")->fetch_assoc()['c']; ?>
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i class="fa-solid fa-hand-holding"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Pending Returns</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-1">
                             <?php echo $conn->query("SELECT COUNT(*) as c FROM transactions WHERE status='issued' AND due_date < CURDATE()")->fetch_assoc()['c']; ?>
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">Issued Books Management</h3>
            </div>
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                    <tr>
                        <th class="p-4">Student</th>
                        <th class="p-4">Book Title</th>
                        <th class="p-4">Due Date</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
               <tbody>
    <?php
    $sql = "SELECT t.id, t.student_name, b.title, t.due_date, t.status 
            FROM transactions t 
            JOIN books b ON t.book_id = b.id 
            ORDER BY t.id DESC LIMIT 10";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Determine status color
            $isOverdue = (strtotime($row['due_date']) < time() && $row['status'] == 'issued');
            $statusColor = $row['status'] == 'returned' ? 'text-green-500' : ($isOverdue ? 'text-red-500' : 'text-orange-500');
            
            echo "<tr class='hover:bg-slate-50'>
                <td class='p-4 font-bold text-slate-700'>{$row['student_name']}</td>
                <td class='p-4'>{$row['title']}</td>
                <td class='p-4 text-sm'>{$row['due_date']}</td>
                <td class='p-4 font-bold text-xs uppercase $statusColor'>{$row['status']}</td>
                <td class='p-4'>";
                
            if ($row['status'] == 'issued') {
                echo "<a href='?return={$row['id']}' class='bg-slate-900 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-600 transition-colors'>Return</a>";
            } else {
                echo "<span class='text-slate-300 text-xs'><i class='fa-solid fa-check'></i> Done</span>";
            }
            
            echo "</td></tr>";
        } // <--- THIS WAS LIKELY MISSING OR MISPLACED
    } else {
        echo "<tr><td colspan='5' class='p-8 text-center text-slate-400'>No transactions yet.</td></tr>";
    }
    ?>
</tbody>