
<?php

$uploadDir = __DIR__ . "/uploads/";
$allowedFile = "browser_audit.txt";
$maxSize = 5 * 1024 * 1024; // 5 MB

// Only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed");
}

// Check uploaded file
if (!isset($_FILES["file"])) {
    http_response_code(400);
    exit("No file received");
}

$file = $_FILES["file"];

// Check upload errors
if ($file["error"] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit("Upload error: " . $file["error"]);
}

// Validate filename
if ($file["name"] !== $allowedFile) {
    http_response_code(400);
    exit("Invalid filename");
}

// Validate extension
if (strtolower(pathinfo($file["name"], PATHINFO_EXTENSION)) !== "txt") {
    http_response_code(400);
    exit("Only TXT files are allowed");
}

// Validate file size
if ($file["size"] > $maxSize) {
    http_response_code(413);
    exit("File exceeds 5 MB limit");
}

// Create upload directory if needed
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0750, true)) {
        http_response_code(500);
        exit("Could not create upload directory");
    }
}

// Generate unique filename
$baseName = "browser_audit";
$extension = ".txt";
$counter = 0;

do {
    $newFileName = $counter === 0
        ? $baseName . $extension
        : $baseName . "_" . $counter . $extension;

    $destination = $uploadDir . $newFileName;
    $counter++;

} while (file_exists($destination));

// Move uploaded file
if (!move_uploaded_file($file["tmp_name"], $destination)) {
    http_response_code(500);
    exit("Could not save uploaded file");
}

// Success
http_response_code(200);

echo "Upload successful: " .
     htmlspecialchars($newFileName, ENT_QUOTES, "UTF-8");

?>
