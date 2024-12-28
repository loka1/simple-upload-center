<?php
$servername = "localhost";
$username = "user_name";
$password = "password";
$dbname = "image_upload";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hash = $conn->real_escape_string($_GET['hash']);
$sql = "SELECT * FROM images WHERE hash='$hash'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    unlink($row['filepath']);
    $sql = "DELETE FROM images WHERE hash='$hash'";
    if ($conn->query($sql) === TRUE) {
        echo "Image deleted successfully.";
    } else {
        echo "Error deleting image: " . $conn->error;
    }
} else {
    echo "Image not found.";
}

$conn->close();
?>