<?php
session_start();  // Start session to access cart

// If the cart is empty
if (empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
} else {
    // Display the cart items
    echo "<h2>Your Cart</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";

    // Loop through each cart item
    $total_price = 0;
    foreach ($_SESSION['cart'] as $item_id => $quantity) {
        // Fetch the item details from the database
        $conn = new mysqli("localhost", "root", "", "food_ordering_system");
        $sql = "SELECT * FROM menu_items WHERE id = $item_id";
        $result = $conn->query($sql);
        $item = $result->fetch_assoc();

        if ($item) {
            $item_total = $item['price'] * $quantity;
            $total_price += $item_total;

            echo "<tr>";
            echo "<td>" . $item['name'] . "</td>";
            echo "<td>" . $quantity . "</td>";
            echo "<td>₹" . $item['price'] . "</td>";
            echo "<td>₹" . $item_total . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "<h3>Total: ₹" . $total_price . "</h3>";

    // Optionally, add a checkout button
    echo "<a href='checkout.php'>Proceed to Checkout</a>";
}
?>
