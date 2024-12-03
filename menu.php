<?php
session_start(); // Start the session to handle cart items

// If the cart doesn't exist, create an empty cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart (when form is submitted)
if (isset($_GET['id']) && isset($_GET['quantity'])) {
    $item_id = $_GET['id'];
    $quantity = $_GET['quantity'];

    // If the item is already in the cart, update the quantity
    if (array_key_exists($item_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        // Otherwise, add the item to the cart
        $_SESSION['cart'][$item_id] = $quantity;
    }
}

// Handle Delete Item from Cart
if (isset($_GET['delete'])) {
    $item_id_to_delete = $_GET['delete'];
    // Remove the item from the cart
    unset($_SESSION['cart'][$item_id_to_delete]);
}

// Fetch the menu items from the database
$conn = new mysqli("localhost", "root", "", "food_ordering_system");
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

// Fetch cart items from the session
$cart_items = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .button:hover {
            background-color: #4CAF50;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-8 p-4">
        <!-- Header Section -->
        <h2 class="text-4xl font-bold text-center text-blue-600 mb-8">Delicious Menu</h2>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="card bg-white shadow-lg rounded-lg overflow-hidden transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-blue-700"><?= $item['name'] ?></h3>
                        <p class="text-gray-600 mt-2"><?= $item['description'] ?></p>
                        <p class="font-bold text-xl text-gray-800 mt-4">₹<?= $item['price'] ?></p>

                        <!-- Add to Cart Form -->
                        <form action="menu.php" method="GET" class="mt-6 flex items-center space-x-4">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" class="border p-2 rounded-lg w-16 focus:ring-2 focus:ring-blue-600">
                            <button type="submit" class="button bg-blue-600 text-white px-6 py-2 rounded-lg w-full mt-4 transform transition-all duration-200 hover:bg-blue-700">Add to Cart</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Cart Section -->
        <h2 class="text-2xl font-bold mt-8 text-center text-blue-600">Your Cart</h2>
        <?php if (count($cart_items) > 0): ?>
            <div class="bg-white shadow-lg rounded-lg p-4 mt-6">
                <table class="w-full table-auto text-left">
                    <thead>
                        <tr>
                            <th class="border p-2 text-lg font-semibold">Item</th>
                            <th class="border p-2 text-lg font-semibold">Quantity</th>
                            <th class="border p-2 text-lg font-semibold">Price</th>
                            <th class="border p-2 text-lg font-semibold">Total</th>
                            <th class="border p-2 text-lg font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_price = 0;
                        foreach ($cart_items as $item_id => $quantity) {
                            $sql = "SELECT * FROM menu_items WHERE id = $item_id";
                            $result = $conn->query($sql);
                            $item = $result->fetch_assoc();

                            if ($item) {
                                $item_total = $item['price'] * $quantity;
                                $total_price += $item_total;
                                echo "<tr>";
                                echo "<td class='border p-2'>" . $item['name'] . "</td>";
                                echo "<td class='border p-2'>" . $quantity . "</td>";
                                echo "<td class='border p-2'>₹" . $item['price'] . "</td>";
                                echo "<td class='border p-2'>₹" . $item_total . "</td>";
                                echo "<td class='border p-2'>
                                        <a href='menu.php?delete=$item_id' class='text-red-600 hover:text-red-800'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="border p-2 text-right font-bold text-xl">Total:</td>
                            <td class="border p-2 font-bold text-xl">₹<?= $total_price ?></td>
                        </tr>
                    </tbody>
                </table>
                <a href="checkout.php" class="button bg-green-600 text-white px-6 py-3 rounded-lg mt-6 w-full text-center hover:bg-green-700">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p class="mt-4 text-center text-gray-500">Your cart is empty.</p>
        <?php endif; ?>
    </div>

</body>
</html>
