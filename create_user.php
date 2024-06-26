<?php
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$new_username = 'admin';
$new_password = 'adminpassword';

// Hacher le mot de passe
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Insérer l'utilisateur dans la base de données
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $new_username, $hashed_password);

if ($stmt->execute()) {
    echo "Nouvel utilisateur créé avec succès.";
} else {
    echo "Erreur lors de la création de l'utilisateur : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
    