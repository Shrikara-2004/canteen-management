<?php
session_start();
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty!";
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_ordering_system";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate total price
$total_amount = 0;
foreach ($_SESSION['cart'] as $item_id) {
    $sql = "SELECT price FROM menu_items WHERE id = $item_id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $total_amount += $row['price'];
    }
}

// Insert order
$sql = "INSERT INTO orders (customer_name, customer_email, total_amount) VALUES ('John Doe', 'john@example.com', $total_amount)";
$conn->query($sql);

$order_id = $conn->insert_id;

// Add items to order_items
foreach ($_SESSION['cart'] as $item_id) {
    $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES ($order_id, $item_id, 1)";
    $conn->query($sql);
}

$conn->close();

// Clear cart
unset($_SESSION['cart']);

echo "Order placed successfully!";
?>
