<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$form_id = $_POST['form_id'] ?? null; // Check if form_id is provided

// Database connection
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marque = $_POST['marque'] ?? null;
    $imei = $_POST['imei'] ?? null;
    $prix = $_POST['prix'] ?? null;

    // Debugging: Check if form data is being received
    if (!$form_id || !$marque || !$imei || !$prix) {
        die("Error: Missing form data. Form ID: " . var_export($form_id, true) . ", Marque: " . var_export($marque, true) . ", IMEI: " . var_export($imei, true) . ", Prix: " . var_export($prix, true));
    }

    // Retrieve the next id for the given user_id and form_id
    $stmt = $conn->prepare("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM phones WHERE user_id = ? AND form_id = ?");
    $stmt->bind_param("ii", $user_id, $form_id);
    $stmt->execute();
    $stmt->bind_result($next_id);
    $stmt->fetch();
    $stmt->close();

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO phones (user_id, form_id, id, marque, imei, prix) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissd", $user_id, $form_id, $next_id, $marque, $imei, $prix);

    if ($stmt->execute()) {
        echo "Phone entry created successfully.";
    } else {
        echo "Error during insertion: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
