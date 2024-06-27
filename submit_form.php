<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = empty($_POST['date']) ? date('Y-m-d H:i:s') : $_POST['date'];
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

    // Trouver le prochain id pour cet utilisateur
    $result = $conn->query("SELECT IFNULL(MAX(id), 0) + 1 AS next_id FROM form WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    $form_id = $row['next_id'];

    // Insertion dans la table form
    $stmt = $conn->prepare("INSERT INTO form (user_id, id, date, var, nom, prenom, autre, autre2, autre3, autre4, autre5, autre6, DebutAcc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssssssssss", $user_id, $form_id, $date, $var, $nom, $prenom, $autre, $autre2, $autre3, $autre4, $autre5, $autre6, $debutAcc);

    if ($stmt->execute()) {
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

        foreach ($phones as $phone) {
            $stmt_phone = $conn->prepare("INSERT INTO phones (user_id, form_id, marque, imei, prix) VALUES (?, ?, ?, ?, ?)");
            $stmt_phone->bind_param("iisss", $user_id, $form_id, $phone['marque'], $phone['imei'], $phone['prix']);
            $stmt_phone->execute();
        }

        $queryParams = http_build_query([
            'user_id' => $user_id,
            'form_id' => $form_id,
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
