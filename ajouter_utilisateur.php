<?php
// Commencer la session si nécessaire
session_start();

if (isset($_POST['ajouter'])) {
    $login = $_POST['login'];
    $pwd = $_POST['password'];

    if (!empty($login) && !empty($pwd)) {
        require_once 'include/database.php';

        // Vérifier si l'utilisateur existe déjà dans la base de données
        $sqlCheck = $pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE login = ?');
        $sqlCheck->execute([$login]);
        $userExists = $sqlCheck->fetchColumn();

        if ($userExists > 0) {
            // Si l'utilisateur existe déjà, afficher un message d'erreur
            $error_message = 'Un utilisateur avec ce login existe déjà.';
        } else {
            // Si l'utilisateur n'existe pas, on l'ajoute à la base de données
            $date = date('Y-m-d');
            $sqlState = $pdo->prepare('INSERT INTO utilisateur (login, password, date_creation) VALUES (?, ?, ?)');
            $sqlState->execute([$login, $pwd, $date]);

            // Redirection après ajout de l'utilisateur
            header('Location: connexion.php');
            exit(); // Important d'ajouter un exit pour arrêter le script après la redirection
        }
    } else {
        // Afficher le message d'erreur seulement après tout le traitement PHP
        $error_message = 'Login et mot de passe sont obligatoires.';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <?php include 'include/head.php' ?>
    <title>Ajouter utilisateur</title>
    <!-- Ajout du CSS moderne -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts pour améliorer la typographie -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        /* Styles pour une meilleure typographie et une interface fluide */
        body {
            background-image: url('https://img.freepik.com/free-vector/white-abstract-background_23-2148809724.jpg?t=st=1731705824~exp=1731709424~hmac=01eea458eb6633dabb1c15395cd132eca67d821716a792a86dda728f83625b42&w=1060'); 
            background-size: cover;
            background-position: center;
            height: 100vh;
        }

        .container {
            margin-top: 10vh; /* Positionne la carte au centre verticalement */
        }

        .card {
            background-color: #ffffff; /* Fond blanc pour la carte */
            border-radius: 20px; /* Coins arrondis */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Ombre douce */
        }

        .card h4 {
            color: #343a40; /* Gris foncé pour le titre */
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .form-label {
            color: #6c757d; /* Gris clair pour les labels */
            font-weight: 500;
        }

        .form-control {
            background-color: #f8f9fa; /* Fond gris clair pour les champs */
            border: 1px solid #ced4da; /* Bordure gris clair */
            color: #495057; /* Texte gris foncé */
            border-radius: 10px; /* Coins arrondis des champs */
        }

        .form-control:focus {
            background-color: #ffffff; /* Fond blanc au focus */
            border-color: #6c757d; /* Bordure gris plus foncé au focus */
            box-shadow: 0 0 5px rgba(108, 117, 125, 0.5); /* Légère ombre gris foncé */
        }

        .btn-primary {
            background-color: #6c757d; /* Bouton gris */
            border-color: #6c757d;
            border-radius: 25px; /* Coins arrondis pour le bouton */
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #5a6268; /* Gris plus foncé au survol */
            border-color: #4e555b;
        }

        .alert-danger {
            background-color: #f8d7da; /* Fond rouge pâle pour les alertes */
            border-color: #f5c6cb; /* Bordure rouge pâle */
            color: #721c24; /* Texte rouge */
            font-weight: 500;
        }

        .mt-3 a {
            color: #6c757d; /* Lien gris */
            font-weight: 500;
        }

        .mt-3 a:hover {
            text-decoration: underline;
            color: #5a6268; /* Lien gris plus foncé au survol */
        }

    </style>
</head>
<body>
<?php include 'include/nav.php' ?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 20px;">
        <h4 class="text-center mb-4">Ajouter un utilisateur</h4>
        
        <!-- Affichage du message d'erreur -->
        <?php
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>

        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2" name="ajouter">
                <i class="fas fa-user-plus"></i> Ajouter utilisateur
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="connexion.php" class="text-muted">Retour à la connexion</a>
        </div>
    </div>
</div>

<!-- Ajout de Bootstrap JS et dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
