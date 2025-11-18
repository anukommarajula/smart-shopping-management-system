<?php
$host = 'sql100.infinityfree.com';  // ← CHANGE THIS
$dbname = 'if0_40447570_if0_40447570_shopping';  // ← CHANGE THIS
$user = 'if0_40447570';  // ← CHANGE THIS
$password = '';  // ← KEEP EMPTY

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);  // ← REMOVE port=3307
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Optional
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
