<?php 
include 'db_config.php'; 

// Logic: Quick Restock (+5 copies)
if(isset($_GET['restock_id'])) {
    $id = $_GET['restock_id'];
    $conn->query("UPDATE books SET quantity = quantity + 5, available_qty = available_qty + 5 WHERE id = $id");
    header("Location: manage_inventory.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">📦 Master Inventory</h1>
            <a href="add_book.php" class="bg-red-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg shadow-red-200 hover:bg-red-700 transition-all">
                + Add New Title
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-900 text-slate-400 text-xs uppercase tracking-widest font-black">
                    <tr>
                        <th class="p-5">Book Info</th>
                        <th class="p-5">Category</th>
                        <th class="p-5">Stock Level</th>
                        <th class="p-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    $result = $conn->query("SELECT * FROM books ORDER BY title ASC");
                    while($row = $result->fetch_assoc()):
                        // Smart Stock Alert
                        $isLow = ($row['available_qty'] <= 2);
                        $barColor = $isLow ? 'bg-red-500' : 'bg-green-500';
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-5">
                            <p class="font-bold text-slate-800"><?php echo $row['title']; ?></p>
                            <p class="text-xs text-slate-400"><?php echo $row['author']; ?></p>
                        </td>
                        <td class="p-5">
                            <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black uppercase text-slate-500">
                                Computer Science
                            </span>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold <?php echo $isLow ? 'text-red-600' : 'text-slate-600'; ?>">
                                    <?php echo $row['available_qty']; ?> / <?php echo $row['quantity']; ?>
                                </span>
                                <div class="w-24 h-2 bg-slate-100 rounded-full">
                                    <div class="h-full <?php echo $barColor; ?> rounded-full" style="width: <?php echo ($row['available_qty']/$row['quantity'])*100; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 flex gap-3">
                            <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit Info">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="?restock_id=<?php echo $row['id']; ?>" class="text-green-500 hover:text-green-700" title="Quick Restock +5">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>