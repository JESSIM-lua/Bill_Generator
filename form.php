<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nvv.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
    <title>Site PHP</title>
    <style>
        /* Ajoutez vos styles CSS personnalisés ici */
        .form-group {
            margin-bottom: 10px;
        }
        .btnn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btnn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <label class="switch" id="darkm">
        <input onclick="darkmode()" type="checkbox" id="darkModeToggle">
        <span class="slider round"></span>
    </label>

    <form id="myForm" action="submit_form.php" method="post">
    <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="date">Entrez la Date:</label>
                <input type="date" name="date">
            </div>
            <div class="form-group">
                <label for="nom">Entrez Le Nom du Client :</label>
                <input type="text" name="nom" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="prenom">Entrez Le Prénom du Client :</label>
                <input type="text" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="autre4">Entrez l'adresse du client:</label>
                <input type="text" name="autre4" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="autre5">Entrez le code postal:</label>
                <input type="text" name="autre5" required>
            </div>
            <div class="form-group">
                <label for="autre6">Entrez la ville:</label>
                <input type="text" name="autre6" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="autre">Numéro de Téléphone:</label>
                <input type="text" name="autre" required>
            </div>
            <div class="form-group">
                <label for="autre2"> Produit:</label>
                <input type="text" name="autre2" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="autre3">Remarque:</label>
                <input type="text" name="autre3">
            </div>
            <div class="form-group">
                <label for="var">Type de Service:</label>
                <select name="var" required>
                    <option value="réparation">réparation</option>
                    <option value="achat">achat</option>
                    <option value="panne">panne</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="DebutAcc">Prix:</label>
                <input type="text" name="DebutAcc" placeholder="€" required>
            </div>
        </div>

        <div id="phones">
            <!-- Zone pour les téléphones ajoutés dynamiquement -->
        </div>

        <div class="form-buttons">
            <button type="button" onclick="ajouterTelephone()" class="btnn">
                <svg height="24" width="24" fill="#FFFFFF" viewBox="0 0 24 24" data-name="Layer 1" id="Layer_1" class="sparkle">
                    <path d="M10,21.236,6.755,14.745.264,11.5,6.755,8.255,10,1.764l3.245,6.491L19.736,11.5l-6.491,3.245ZM18,21l1.5,3L21,21l3-1.5L21,18l-1.5-3L18,18l-3,1.5ZM19.333,4.667,20.5,7l1.167-2.333L24,3.5,21.667,2.333,20.5,0,19.333,2.333,17,3.5Z"></path>
                </svg>
                <span class="text">Ajouter Téléphone</span>
            </button>
        </div><br>

        <div class="form-buttons">
            <button type="submit" class="btnn">
                <svg height="24" width="24" fill="#FFFFFF" viewBox="0 0 24 24" data-name="Layer 1" id="Layer_1" class="sparkle">
                    <path d="M10,21.236,6.755,14.745.264,11.5,6.755,8.255,10,1.764l3.245,6.491L19.736,11.5l-6.491,3.245ZM18,21l1.5,3L21,21l3-1.5L21,18l-1.5-3L18,18l-3,1.5ZM19.333,4.667,20.5,7l1.167-2.333L24,3.5,21.667,2.333,20.5,0,19.333,2.333,17,3.5Z"></path>
                </svg>
                <span class="text">Télécharger PDF</span>
            </button>
        </div>
    </form>

    <div class="form-buttons">
        <button class="btnn" id="admin">PAGE ADMIN</button>
    </div>
</div>

<script>
    function darkmode() {
        var element = document.body;
        element.classList.toggle("dark-mode");
    }

    var adminButton = document.getElementById("admin");
    adminButton.addEventListener("click", function() {
        window.location.href = "admin.php";
    });

    function ajouterTelephone() {
        var phonesDiv = document.getElementById('phones');
        var index = phonesDiv.children.length + 1;

        var newPhoneDiv = document.createElement('div');
        newPhoneDiv.className = 'form-group';
        newPhoneDiv.innerHTML = `
            <label>Téléphone ${index}:</label>
            <input type="text" name="autre2_${index}" placeholder="Produit" required><br>
            <input type="text" name="autre3_${index}" placeholder="Remarque" ><br>
            <input type="text" name="prix_${index}" placeholder="€" required>
            <button type="button" class="btnn btnn-small" onclick="supprimerTelephone(this)">Supprimer</button>
            <br>
        `;

        phonesDiv.appendChild(newPhoneDiv);
    }

    function supprimerTelephone(button) {
        var divToRemove = button.parentNode;
        divToRemove.parentNode.removeChild(divToRemove);
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="anim.js"></script>
<script src="scripts.js"></script>
</body>
</html>
