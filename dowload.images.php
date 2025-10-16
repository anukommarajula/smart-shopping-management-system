<?php
$folder = "images/";

// Make sure the folder exists
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// Placeholder image source
$baseURL = "https://via.placeholder.com/300x200.png?text=Product+";

for ($i = 1; $i <= 2500; $i++) {
    $url = $baseURL . $i; // Create unique URL for each product
    $imageData = file_get_contents($url);
    $fileName = $folder . "product$i.jpg";
    file_put_contents($fileName, $imageData);
    echo "Saved: $fileName<br>";
}
