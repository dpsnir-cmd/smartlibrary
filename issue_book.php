<?php
session_start();
include 'db_config.php';

// HANDLE THE ISSUANCE LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $book_id = $_POST['book_id'];
    $due_date = $_POST['due_date'];
    $issue_date = date('Y-m-d');

    // 1. Insert into transactions
    $sql = "INSERT INTO transactions (student_name, book_id, issue_date, due_date, status) 
            VALUES ('$student_name', $book_id, '$issue_date', '$due_date', 'issued')";
    
    if ($conn->query($sql) === TRUE) {
        // 2. Reduce the available quantity in the books table
        $conn->query("UPDATE books SET available_qty = available_qty - 1 WHERE id = $book_id");
        
        echo "<script>alert('Book issued successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Issue Book | SmartX Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
        <div class="bg-slate-900 p-8 text-white text-center relative">
            <div class="absolute top-4 right-4 text-slate-500 hover:text-white">
                <a href="admin_dashboard.php"><i class="fa-solid fa-xmark text-xl"></i></a>
            </div>
            <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-red-600/40">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <h2 class="text-2xl font-black uppercase tracking-tight">Issue <span class="text-red-500">Book</span></h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">New Transaction Record</p>
        </div>

        <form method="POST" class="p-8 space-y-6">
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Student Name</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="student_name" required placeholder="Full Name of Student" 
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all font-bold text-slate-700">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Select Book</label>
                <div class="relative">
                    <i class="fa-solid fa-book absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="book_id" required class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all font-bold text-slate-700 appearance-none">
                        <option value="">Select an available book...</option>
                        <?php
                        $books = $conn->query("SELECT id, title FROM books WHERE available_qty > 0");
                        while($b = $books->fetch_assoc()) {
                            echo "<option value='{$b['id']}'>{$b['title']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Due Date</label>
                <div class="relative">
                    <i class="fa-solid fa-calendar-day absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="due_date" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all font-bold text-slate-700">
                </div>
                <p class="text-[9px] text-slate-400 mt-2 italic">* Default set to 7 days from today.</p>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-red-600 transition-all shadow-xl shadow-slate-200">
                Confirm Issuance
            </button>
        </form>
    </div>

</body>
</html>