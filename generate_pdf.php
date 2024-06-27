<?php

if (!isset($_GET['user_id']) || !isset($_GET['form_id'])) {
    die("Les paramètres 'user_id' et 'form_id' sont manquants.");
}

$user_id = $_GET['user_id'];
$form_id = $_GET['form_id'];

// Connexion à la base de données
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "facture";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$date = $_GET['date'];
$prenom = $_GET['prenom'];
$nom = $_GET['nom'];
$var = $_GET['var'];
$autre = $_GET['autre'];
$autre2 = $_GET['autre2'];
$autre3 = $_GET['autre3'];
$autre4 = $_GET['autre4'];
$autre5 = $_GET['autre5'];
$autre6 = $_GET['autre6'];
$debutAcc = $_GET['DebutAcc'];

// Inclusion de la classe TCPDF
require_once('tcpdf/tcpdf.php');

// Récupérer les téléphones supplémentaires depuis la base de données
$phones = [];
$stmt_phones = $conn->prepare("SELECT marque, imei, prix FROM phones WHERE user_id = ? AND form_id = ?");
$stmt_phones->bind_param("ii", $user_id, $form_id);
$stmt_phones->execute();
$result_phones = $stmt_phones->get_result();
while ($row = $result_phones->fetch_assoc()) {
    $phones[] = $row;
}

// Calcul du total des prix
$total = $debutAcc;
foreach ($phones as $phone) {
    $total += $phone['prix'];
}

// Création d'une nouvelle instance de TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Nom du fichier PDF à générer
$nom_fichier = 'facture_' . $nom . '_' . $prenom . '.pdf';

// Ajout d'une nouvelle page au PDF avec des marges réduites
$pdf->SetMargins(5, 5, 5);
$pdf->AddPage();

$style = '
<style>
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    font-size: 8px;
}

h1 {
    text-align: center;
    margin-bottom: 1px;
    font-size: 48px;
}

.container {
    margin: 0;
    padding: 0;
    border: 1px solid #000;
    border-radius: 5px;
}

.header, .info, .right-div {
    margin: 2px 0;
    padding: 0;
    border: 1px solid #000;
    border-radius: 2px;
}

.footer {
    border: 1px solid #000;
    border-radius: 2px;
    text-align: left;
    margin: 0;
    padding: 0;
}

.date {
    text-align: right;
    margin: 0;
    padding: 0;
}

.info p, .right-div p {
    margin: 1px 0;
}

.petit {
    font-size: 6px;
    text-align: justify;
}

.montants {
    display: flex;
    justify-content: space-between;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #000;
    text-align: center;
    padding: 5px;
}

th {
    font-weight: bold;
}
</style>';

// Construction du contenu HTML à inclure dans le PDF
$html = '
' . $style . '
    <img src="logo.png" width="80" height="80">
    <p class="date">' . $date . '</p>
    <p>Fix My Phone<br>26 Cours Gambetta<br>69007 Lyon<br>Tél : 09 82 27 39 93<br></p><p class="date"><br><strong>' . $nom . ' ' . $prenom . '</strong><br>' . $autre4 . '<br> ' . $autre5 . ' ' . $autre6 . '<br>Tél : ' . $autre . '<br></p>
        <h1>Facture n°' . $form_id . '</h1>
        <div class="container">
<table>
<thead>
    <tr>
        <th>Produit</th>
        <th>PRIX</th>
        <th>QTÉ</th>
        <th>MONTANT</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>' . $autre2 . ($autre3 ? ' <br> Remarque: ' . $autre3 : '') . '</td>
        <td>' . $debutAcc . '</td>
        <td>1</td>
        <td>' . $debutAcc . '€</td>
    </tr>';

foreach ($phones as $phone) {
    $html .= '
    <tr>
        <td>' . htmlspecialchars($phone['marque']) . ($phone['imei'] ? ' <br> Remarque: ' . htmlspecialchars($phone['imei']) : '') . '</td>
        <td>' . htmlspecialchars($phone['prix']) . '</td>
        <td>1</td>
        <td>' . htmlspecialchars($phone['prix']) . '€</td>
    </tr>';
}

$html .= '
</tbody>
<tfoot>
    <tr>
        <td colspan="3" style="text-align: right;">HTTC: </td>
        <td>' . $total * 0.8 . '€</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right;">TVA</td>
        <td>20 %</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right;">TOTAL</td>
        <td>' . $total . '€</td>
    </tr>
</tfoot>
</table>
</div>
<br>

    <div class="footer">
        <p class="petit">Tout appareil non récupéré dans un délai d\'un mois sera détruit</p>
        <p class="petit">Obligation de présenter ce justificatif pour la remise de l\'appareil</p>
        <p class="petit">3 mois de garantie sur toute réparation hors casse</p>
        <p class="petit">Sans présentation de ce justificatif aucune garantie ne sera prise</p>
        <p class="petit">Aucune garantie fournie pour les téléphones oxydés</p>
        <p class="petit">La maison n\'est pas responsable de toute perte des protections</p>
    </div>
';

// Ajout du contenu HTML dans le PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Génération du fichier PDF et envoi au navigateur pour téléchargement
$pdf->Output($nom_fichier, 'D');

$conn->close();
?>
