<?php
session_start();

// Ensure the cart exists, if not initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch the cart items from the session
$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;

// If the cart is empty, display an error message
if (empty($cart_items)) {
    $cart_empty_error = "Your cart is empty. Please add items before proceeding.";
} else {
    $cart_empty_error = "";
}

// Fetch items from the database based on the cart
$conn = new mysqli("localhost", "root", "", "food_ordering_system");

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all cart items at once to minimize database calls
$item_ids = implode(',', array_keys($cart_items));
$sql = "SELECT * FROM menu_items WHERE id IN ($item_ids)";
$result = $conn->query($sql);

$menu_items = [];
while ($item = $result->fetch_assoc()) {
    $menu_items[$item['id']] = $item;
}

// Calculate the total price
foreach ($cart_items as $item_id => $quantity) {
    if (isset($menu_items[$item_id])) {
        $total_price += $menu_items[$item_id]['price'] * $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Food Ordering System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-custom {
            background-image: url('https://images.unsplash.com/photo-1580158402753-8cc0e63cc5c0');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-gray-100 bg-custom">

    <header class="bg-blue-600 text-white py-4">
        <h1 class="text-4xl font-extrabold text-center">SMVITM CANTEEN</h1>
    </header>

    <div class="container mx-auto mt-16 p-6 bg-white rounded-lg shadow-lg">

        <h2 class="text-3xl font-bold text-center mb-8">Checkout</h2>

        <?php if ($cart_empty_error): ?>
            <div class="bg-red-500 text-white text-center p-4 mb-8 rounded">
                <strong>Error:</strong> <?= $cart_empty_error ?>
            </div>
        <?php endif; ?>

        <!-- Cart Summary -->
        <?php if (!$cart_empty_error): ?>
            <div class="mb-8">
                <h3 class="text-2xl font-semibold mb-4">Your Cart</h3>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="p-4 text-left">Item</th>
                            <th class="p-4 text-left">Quantity</th>
                            <th class="p-4 text-left">Price</th>
                            <th class="p-4 text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item_id => $quantity): ?>
                            <?php if (isset($menu_items[$item_id])): ?>
                                <tr class="border-b">
                                    <td class="p-4"><?= $menu_items[$item_id]['name'] ?></td>
                                    <td class="p-4"><?= $quantity ?></td>
                                    <td class="p-4">₹<?= $menu_items[$item_id]['price'] ?></td>
                                    <td class="p-4">₹<?= $menu_items[$item_id]['price'] * $quantity ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="p-4 text-right font-bold">Total:</td>
                            <td class="p-4 font-bold">₹<?= $total_price ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment Method Section -->
            <form action="order_confirmation.php" method="POST">
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold mb-4">Select Payment Method</h3>
                    <div class="flex space-x-6">
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" id="cash" class="h-5 w-5" checked>
                            <label for="cash" class="text-lg">Cash on Delivery</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" id="upi" class="h-5 w-5">
                            <label for="upi" class="text-lg">UPI</label>
                        </div>
                    </div>
                </div>

                <div id="upi-details" class="hidden mb-8">
                    <h4 class="text-lg font-semibold">Enter UPI ID</h4>
                    <input type="text" id="upi_id" name="upi_id" placeholder="Enter your UPI ID" class="border p-3 mt-2 w-full rounded-lg" />
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-lg px-8 py-3 rounded-lg transition-all duration-300">
                        Confirm Order
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <footer class="bg-blue-600 text-white text-center py-4 mt-8">
        <p>&copy; 2024 SMVITM Canteen. All Rights Reserved.</p>
    </footer>

    <script>
        // Toggle UPI input visibility based on the selected payment method
        const cashOption = document.getElementById('cash');
        const upiOption = document.getElementById('upi');
        const upiDetails = document.getElementById('upi-details');

        cashOption.addEventListener('change', () => {
            upiDetails.classList.add('hidden');
        });

        upiOption.addEventListener('change', () => {
            upiDetails.classList.remove('hidden');
        });
    </script>

</body>
</html>
