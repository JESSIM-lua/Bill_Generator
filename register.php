<?php
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Préparer et exécuter la requête d'insertion pour la table users
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        // Rediriger vers la page de connexion avec un message de succès
        header("Location: login.php?message=Utilisateur créé avec succès");
        exit;
    } else {
        echo "Erreur lors de l'insertion : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
