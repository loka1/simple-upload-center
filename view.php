<?php
$servername = "localhost";
$username = "user_name";
$password = "password";
$dbname = "image_upload";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM images WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<img src='" . $row['filepath'] . "' alt='" . $row['filename'] . "'>";
} else {
    echo "Image not found.";
}

$conn->close();
?>