<?php
session_start();  // Start session to store cart data

// Get the item ID and quantity from the URL or form
if (isset($_GET['id']) && isset($_GET['quantity'])) {
    $item_id = $_GET['id'];
    $quantity = $_GET['quantity'];

    // If the cart does not exist, create it
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the item already exists in the cart
    if (array_key_exists($item_id, $_SESSION['cart'])) {
        // If the item already exists, update the quantity
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        // Otherwise, add the item to the cart
        $_SESSION['cart'][$item_id] = $quantity;
    }

    // Redirect back to the menu or cart page
    header("Location: menu.html");  // Redirect to menu page or cart page
    exit();
} else {
    // If the necessary data is not provided, redirect back to the menu
    header("Location: menu.html");
    exit();
}
?>

