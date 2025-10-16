<?php
$host = '127.0.0.1'; // Use IP instead of localhost
$dbname = 'smart shopping management system';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=3307;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Optional
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); // use die() to stop script
}
?>