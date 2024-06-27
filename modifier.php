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
    $user_id = $_POST['user_id']; // Assurez-vous que ce champ est inclus dans le formulaire
    $id = $_POST['id'];
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $autre = $_POST['autre'];
    $autre2 = $_POST['autre2'];
    $autre3 = $_POST['autre3'];
    $autre4 = $_POST['autre4'];
    $autre5 = $_POST['autre5'];
    $autre6 = $_POST['autre6'];
    $var = $_POST['var'];
    $debutAcc = $_POST['DebutAcc'];

    // Préparer et exécuter la requête de mise à jour pour la table form
    $stmt = $conn->prepare("UPDATE form SET date = ?, var = ?, nom = ?, prenom = ?, autre = ?, autre2 = ?, autre3 = ?, autre4 = ?, autre5 = ?, autre6 = ?, DebutAcc = ? WHERE user_id = ? AND id = ?");
    if ($stmt === false) {
        die("Erreur lors de la préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("ssssssssssssi", $date, $var, $nom, $prenom, $autre, $autre2, $autre3, $autre4, $autre5, $autre6, $debutAcc, $user_id, $id);

    if ($stmt->execute()) {
        // Gérer les téléphones existants et nouveaux
        foreach ($_POST as $key => $value) {
            if (preg_match('/^autre2_\d+$/', $key)) {
                $index = str_replace('autre2_', '', $key);
                $marque = $value;
                $imei = $_POST["autre3_$index"];
                $prix = $_POST["prix_$index"];

                if (isset($_POST["phone_id_$index"])) {
                    // Mettre à jour le téléphone existant
                    $phone_id = $_POST["phone_id_$index"];
                    $stmt_phone = $conn->prepare("UPDATE phones SET marque = ?, imei = ?, prix = ? WHERE user_id = ? AND form_id = ? AND id = ?");
                    if ($stmt_phone === false) {
                        die("Erreur lors de la préparation de la requête de mise à jour du téléphone : " . $conn->error);
                    }
                    $stmt_phone->bind_param("sssiii", $marque, $imei, $prix, $user_id, $id, $phone_id);
                } else {
                    // Insérer un nouveau téléphone
                    $stmt_phone = $conn->prepare("INSERT INTO phones (user_id, form_id, marque, imei, prix) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt_phone === false) {
                        die("Erreur lors de la préparation de la requête d'insertion du téléphone : " . $conn->error);
                    }
                    $stmt_phone->bind_param("iisss", $user_id, $id, $marque, $imei, $prix);
                }
                $stmt_phone->execute();
            }
        }
        echo "Enregistrement mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

echo "<a href='admin.php'>Retour</a>";
?>
