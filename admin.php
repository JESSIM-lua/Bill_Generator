<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Affichage et modification de données</title>
    <script>
        function showEditForm(id) {
            var editForm = document.getElementById('editForm' + id);
            editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dmn.css">
    <link rel="stylesheet" href="resp.css"> 
</head>
<body>
<div class="container">
    <h1>Administration des Factures</h1>
    <form method="GET" action="admin.php" class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher par N° Facture" aria-label="Search" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
    </form>
<?php

$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM form WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Enregistrement supprimé avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression : " . $stmt->error . "</div>";
    }
    $stmt->close();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM form WHERE user_id = ?";
if (!empty($search)) {
    $sql .= " AND id = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $stmt->bind_param("ii", $user_id, $search);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'>
    <table class='table table-striped table-hover'>
    <thead>
    <tr>
    <th>N° Facture</th>
    <th>Date</th>
    <th>Type</th>
    <th>Nom</th>
    <th>Prénom</th>
    <th>N° Téléphone</th>
    <th>Produit</th>
    <th>Remarques</th>
    <th>Adresse</th>
    <th>Code Postal</th>
    <th>Ville</th>
    <th>Prix</th>
    <th>Produits</th>
    <th>Actions</th>
    </tr>
    </thead>
    <tbody>";

    while($row = $result->fetch_assoc()) {
        $facture_id = $row["id"];

        // Récupérer les téléphones supplémentaires pour chaque facture
        $phones = [];
        $stmt_phones = $conn->prepare("SELECT * FROM phones WHERE facture_id = ?");
        $stmt_phones->bind_param("i", $facture_id);
        $stmt_phones->execute();
        $result_phones = $stmt_phones->get_result();
        while ($phone = $result_phones->fetch_assoc()) {
            $phones[] = $phone;
        }

        echo "<tr>
        <td>".$row["id"]."</td>
        <td>".$row["date"]."</td>
        <td>".$row["var"]."</td>
        <td>".$row["nom"]."</td>
        <td>".$row["prenom"]."</td>
        <td>".$row["autre"]."</td>
        <td>".$row["autre2"]."</td>
        <td>".$row["autre3"]."</td>
        <td>".$row["autre4"]."</td>  
        <td>".$row["autre5"]."</td>
        <td>".$row["autre6"]."</td>      
        <td>".$row["DebutAcc"]."</td>
        <td>";

        foreach ($phones as $phone) {
            echo "Produit: " . $phone['marque'] . "<br>Remarque: " . $phone['imei'] . "<br>Prix: " . $phone['prix'] . "<br><br>";
        }

        echo "</td>
        <td>
            <a href='?action=delete&id=".$row["id"]."'>Supprimer</a> | 
            <a href='#' onclick='showEditForm(".$row["id"].")'>Modifier</a> | 
            <a href='generate_pdf.php?id=".$row["id"]."&date=".$row["date"]."&prenom=".$row["prenom"]."&nom=".$row["nom"]."&var=".$row["var"]."&autre=".$row["autre"]."&autre2=".$row["autre2"]."&autre3=".$row["autre3"]."&autre4=".$row["autre4"]."&autre5=".$row["autre5"]."&autre6=".$row["autre6"]."&DebutAcc=".$row["DebutAcc"]."' target='_blank'>Télécharger PDF</a>
        </td>
        </tr>
        <tr id='editForm".$row["id"]."' style='display: none;'>
            <td colspan='13'>
                <form action='modifier.php' method='post'>
                    <input type='hidden' name='id' value='".$row["id"]."'>
                    <div class='form-row'>
                        <input type='text' name='date' value='".$row["date"]."' placeholder='Date'>
                        <input type='text' name='nom' value='".$row["nom"]."' placeholder='Nom'>
                        <input type='text' name='prenom' value='".$row["prenom"]."' placeholder='Prénom'>
                    </div>
                    <div class='form-row'>
                        <input type='text' name='autre' value='".$row["autre"]."' placeholder='N° Téléphone'>
                        <input type='text' name='autre2' value='".$row["autre2"]."' placeholder='Modèle Téléphone'>
                        <input type='text' name='autre3' value='".$row["autre3"]."' placeholder='IMEI'>
                    </div>
                    <div class='form-row'>
                        <input type='text' name='autre4' value='".$row["autre4"]."' placeholder='Adresse'>
                        <input type='text' name='autre5' value='".$row["autre5"]."' placeholder='Code Postal'>
                        <input type='text' name='autre6' value='".$row["autre6"]."' placeholder='Ville'>
                    </div>
                    <div class='form-row'>
                        <input type='text' name='DebutAcc' value='".$row["DebutAcc"]."' placeholder='Prix'>
                        <input type='text' name='var' value='".$row["var"]."' placeholder='Type'>
                    </div>
                    <div id='phones".$facture_id."'>";

        // Affichage des téléphones supplémentaires
        $phone_index = 1;
        foreach ($phones as $phone) {
            echo "
                    <div class='form-row'>
                        <label>Téléphone $phone_index:</label>
                        <input type='hidden' name='phone_id_".$phone_index."' value='".$phone["id"]."'>
                        <input type='text' name='autre2_".$phone_index."' value='".$phone["marque"]."' placeholder='Produit'>
                        <input type='text' name='autre3_".$phone_index."' value='".$phone["imei"]."' placeholder='Remarque'>
                        <input type='text' name='prix_".$phone_index."' value='".$phone["prix"]."' placeholder='Prix'>
                    </div>";
            $phone_index++;
        }
        echo "
                    </div>
                    <button type='button' onclick='ajouterTelephone($facture_id)' class='btn btn-secondary'>Ajouter Téléphone</button><br><br>
                    <input type='submit' value='Enregistrer' class='btn btn-primary'>
                </form>
            </td>
        </tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "<div class='alert alert-info'>0 résultats</div>";
}

$conn->close();
?>
</div>

<script>
    function ajouterTelephone(factureId) {
        var phonesDiv = document.getElementById('phones' + factureId);
        var index = phonesDiv.children.length + 1;

        var newPhoneDiv = document.createElement('div');
        newPhoneDiv.className = 'form-row';
        newPhoneDiv.innerHTML = `
            <label>Téléphone ${index}:</label>
            <input type="hidden" name="new_phone_${index}" value="new">
            <input type="text" name="autre2_${index}" placeholder="Produit" required>
            <input type="text" name="autre3_${index}" placeholder="Remarque" required>
            <input type="text" name="prix_${index}" placeholder="Prix" required>
        `;

        phonesDiv.appendChild(newPhoneDiv);
    }
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
</div>

<script>
    function ajouterTelephone(factureId) {
        var phonesDiv = document.getElementById('phones' + factureId);
        var index = phonesDiv.children.length + 1;

        var newPhoneDiv = document.createElement('div');
        newPhoneDiv.className = 'form-row';
        newPhoneDiv.innerHTML = `
            <label>Téléphone ${index}:</label>
            <input type="hidden" name="new_phone_${index}" value="new">
            <input type="text" name="autre2_${index}" placeholder="Produit" required>
            <input type="text" name="autre3_${index}" placeholder="Remarque" required>
            <input type="text" name="prix_${index}" placeholder="Prix" required>
        `;

        phonesDiv.appendChild(newPhoneDiv);
    }
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>