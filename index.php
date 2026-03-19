<?php
session_start();
include 'db_config.php'; // Ensure your database connection file is correct

// --- SEARCH LOGIC ---
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Secure search to prevent SQL injection (basic version)
    $search_safe = $conn->real_escape_string($search);
    $sql = "SELECT * FROM books WHERE title LIKE '%$search_safe%' OR author LIKE '%$search_safe%'";
} else {
    $sql = "SELECT * FROM books";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JECRC SmartX Library</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { background-color: #f8fafc; }

        /* Glass Navigation */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Card Hover Effects */
        .book-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        /* Tactile Button Animation */
        .btn-tactile:active { transform: scale(0.95); }
    </style>
</head>
<body class="text-slate-900 antialiased selection:bg-red-100 selection:text-red-600">

    <nav class="glass-nav sticky top-0 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-500 rounded-xl rotate-3 flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                <i class="fa-solid fa-bolt-lightning text-lg"></i>
            </div>
            <span class="font-black text-xl tracking-tighter uppercase text-slate-800">
                Smart<span class="text-red-600">X</span>
            </span>
        </div>

        <div class="flex items-center gap-6">
            <a href="index.php" class="text-sm font-bold text-slate-900 hover:text-red-600 transition-colors uppercase tracking-wider hidden md:block">Gallery</a>
            
            <?php if (isset($_SESSION['student'])): ?>
                <a href="student_dashboard.php" class="text-sm font-bold text-slate-500 hover:text-red-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-circle-user text-red-500"></i> My Dashboard
                </a>
                <a href="logout.php" class="bg-red-50 text-red-600 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all btn-tactile">
                    Logout
                </a>
            <?php else: ?>
                <a href="student_login.php" class="text-sm font-bold text-slate-500 hover:text-slate-900 uppercase tracking-wider">Student Login</a>
                <a href="admin_dashboard.php" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 btn-tactile">
                    Librarian
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="relative pt-20 pb-16 px-6 text-center overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-red-600/5 rounded-full blur-3xl -z-10"></div>

        <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight leading-tight">
            Find Your Next <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500">Great Read</span>
        </h1>
        
        <p class="text-slate-500 text-lg mb-10 max-w-2xl mx-auto font-medium">
            Access over <span class="text-slate-900 font-bold"><?php echo $result->num_rows; ?></span> books instantly. Reserve online, pick up in person.
        </p>
        
        <form class="max-w-2xl mx-auto relative group z-20" method="GET" action="">
            <div class="absolute inset-0 bg-red-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
            <div class="relative flex items-center bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-2 ring-1 ring-slate-100 group-focus-within:ring-4 group-focus-within:ring-red-500/10 transition-all">
                
                <i class="fa-solid fa-magnifying-glass text-slate-400 ml-4 text-xl group-focus-within:text-red-500 transition-colors"></i>
                
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by Title, Author, or ISBN..." 
                       class="w-full bg-transparent border-none outline-none py-3 px-4 text-slate-700 font-bold placeholder-slate-400 text-lg h-12">
                
                <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-red-600 transition-colors shadow-lg btn-tactile">
                    Search
                </button>
            </div>
        </form>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Logic: Is the book available?
                    $stock = (int)$row['available_qty'];
                    $isAvailable = ($stock > 0);
            ?>
            
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 book-card group relative overflow-hidden">
                
                <div class="flex justify-between items-start mb-6">
                    <div class="w-14 h-14 rounded-2xl <?php echo $isAvailable ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600'; ?> flex items-center justify-center text-2xl font-bold transition-colors">
                        <i class="fa-solid <?php echo $isAvailable ? 'fa-book-open' : 'fa-clock'; ?>"></i>
                    </div>
                    <?php if(!$isAvailable): ?>
                        <span class="bg-orange-100 text-orange-700 text-[10px] font-black uppercase px-3 py-1.5 rounded-full tracking-widest">
                            Waitlist Open
                        </span>
                    <?php endif; ?>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-2 leading-tight group-hover:text-red-600 transition-colors line-clamp-2">
                    <?php echo $row['title']; ?>
                </h3>
                <p class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-8">
                    by <?php echo $row['author']; ?>
                </p>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-between mt-auto">
                    <div>
                        <p class="text-[10px] uppercase font-black text-slate-400 tracking-tighter">Status</p>
                        <p class="font-bold <?php echo $isAvailable ? 'text-slate-700' : 'text-orange-600'; ?>">
                            <?php echo $isAvailable ? "$stock Available" : "Out of Stock"; ?>
                        </p>
                    </div>

                    <?php if ($isAvailable): ?>
                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center text-lg" title="Available in Library">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="reserve_book.php">
                            <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                            
                            <button type="submit" class="group/btn relative overflow-hidden bg-orange-500 text-white px-5 py-3 rounded-xl font-bold transition-all hover:bg-orange-600 active:scale-95 shadow-lg shadow-orange-500/30">
                                <span class="relative z-10 text-xs uppercase tracking-widest pr-4 group-hover/btn:pr-8 transition-all">Reserve</span>
                                <i class="fa-solid fa-arrow-right absolute right-[-20px] top-1/2 -translate-y-1/2 opacity-0 group-hover/btn:right-3 group-hover/btn:opacity-100 transition-all duration-300"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                } // End While Loop
            } else {
                echo "<div class='col-span-3 text-center py-20'>
                        <div class='inline-block p-6 rounded-full bg-slate-50 mb-4 text-slate-300 text-4xl'><i class='fa-regular fa-face-frown-open'></i></div>
                        <h3 class='text-xl font-bold text-slate-700'>No books found.</h3>
                        <p class='text-slate-400'>Try searching for a different title or author.</p>
                      </div>";
            }
            ?>

        </div>
    </main>

</body>
</html>