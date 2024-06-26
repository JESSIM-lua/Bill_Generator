<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Factures</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #ffafbd, #ffc3a0);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #b10f2e;
            margin-bottom: 20px;
        }

        .btn {
            background-color: #b10f2e;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
            width: calc(100% - 20px);
        }

        .btn:hover {
            background-color: #6a0572;
        }

        .explanation {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Factures</h1>
        <p class="explanation">Bienvenue sur notre site de gestion des factures. Ce site vous permet de créer, gérer et consulter vos factures de manière simple et efficace. Cliquez sur Se Connecter pour accéder à votre compte ou sur S'inscrire pour créer un nouveau compte.</p>
        <button onclick="window.location.href='login.php'" class="btn">Se Connecter</button>
        <button onclick="window.location.href='register.php'" class="btn">S'inscrire</button>
    </div>
</body>
</html>
