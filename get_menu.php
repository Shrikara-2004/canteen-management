<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_ordering_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

$menu = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menu[] = $row;
    }
}

echo json_encode($menu);
$conn->close();
?>
