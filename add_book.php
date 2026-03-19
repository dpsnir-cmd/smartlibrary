<?php 
include 'db_config.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $qty = $_POST['quantity'];
    
    // SQL to insert the book. Note: We set available_qty to be the same as quantity initially.
    $sql = "INSERT INTO books (title, author, quantity, available_qty, status) 
            VALUES ('$title', '$author', $qty, $qty, 'available')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold'>✅ Book added successfully!</div>";
    } else {
        $message = "<div class='bg-red-100 text-red-700 p-4 rounded-xl mb-6'>❌ Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Book | SmartX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-xl w-full bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
        <div class="bg-slate-900 p-8 text-white relative">
            <a href="admin_dashboard.php" class="absolute top-8 right-8 text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </a>
            <h2 class="text-3xl font-bold">Add New Book</h2>
            <p class="text-slate-400 mt-2 text-sm">Expand your library's digital inventory</p>
        </div>

        <div class="p-8">
            <?php echo $message; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Book Title</label>
                    <input type="text" name="title" required placeholder="e.g. Introduction to Algorithms" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Author Name</label>
                    <input type="text" name="author" required placeholder="e.g. Thomas H. Cormen" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Total Copies</label>
                        <input type="number" name="quantity" value="1" min="1" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Category</label>
                        <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                            <option>Computer Science</option>
                            <option>Electronics</option>
                            <option>Mathematics</option>
                            <option>Mechanical</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-red-700 hover:shadow-xl hover:shadow-red-200 transition-all">
                        Add to Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>