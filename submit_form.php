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

// Définir le fuseau horaire de la France
date_default_timezone_set('Europe/Paris');

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si la date est fournie et valide, sinon utiliser la date actuelle
    if (!empty($_POST['date'])) {
        $date = $_POST['date'];
    } else {
        $date = date('Y-m-d');
    }

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $autre4 = $_POST['autre4'];
    $autre5 = $_POST['autre5'];
    $autre6 = $_POST['autre6'];
    $autre = $_POST['autre'];
    $autre2 = $_POST['autre2'];
    $autre3 = $_POST['autre3'];
    $var = $_POST['var'];
    $debutAcc = $_POST['DebutAcc'];

    // Préparer et exécuter la requête d'insertion pour la table form
    $stmt = $conn->prepare("INSERT INTO form (date, var, nom, prenom, autre, autre2, autre3, autre4, autre5, autre6, DebutAcc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $date, $var, $nom, $prenom, $autre, $autre2, $autre3, $autre4, $autre5, $autre6, $debutAcc);

    if ($stmt->execute()) {
        // Récupérer l'ID de la facture insérée
        $facture_id = $stmt->insert_id;

        // Préparer les téléphones supplémentaires
        $phones = [];
        foreach ($_POST as $key => $value) {
            if (preg_match('/^autre2_\d+$/', $key)) {
                $index = str_replace('autre2_', '', $key);
                $phones[$index]['marque'] = $value;
            } elseif (preg_match('/^autre3_\d+$/', $key)) {
                $index = str_replace('autre3_', '', $key);
                $phones[$index]['imei'] = $value;
            } elseif (preg_match('/^prix_\d+$/', $key)) {
                $index = str_replace('prix_', '', $key);
                $phones[$index]['prix'] = $value;
            }
        }

        // Insérer les téléphones supplémentaires dans la table phones
        foreach ($phones as $phone) {
            $stmt_phone = $conn->prepare("INSERT INTO phones (facture_id, marque, imei, prix) VALUES (?, ?, ?, ?)");
            $stmt_phone->bind_param("isss", $facture_id, $phone['marque'], $phone['imei'], $phone['prix']);
            $stmt_phone->execute();
        }

        // Rediriger vers la page de génération du PDF avec les informations nécessaires
        $queryParams = http_build_query([
            'id' => $facture_id,
            'date' => $date,
            'nom' => $nom,
            'prenom' => $prenom,
            'autre4' => $autre4,
            'autre5' => $autre5,
            'autre6' => $autre6,
            'autre' => $autre,
            'autre2' => $autre2,
            'autre3' => $autre3,
            'var' => $var,
            'DebutAcc' => $debutAcc
        ]);

        header("Location: generate_pdf.php?$queryParams");
        exit;
    } else {
        echo "Erreur lors de l'insertion : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
