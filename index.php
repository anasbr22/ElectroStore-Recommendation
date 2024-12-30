
<?php
session_start(); // Démarrer la session

$connecte = false;
$isAdmin = false;

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
    $connecte = true;

    // Vérifier si l'utilisateur est admin
    if (isset($_SESSION['utilisateur']['login']) && $_SESSION['utilisateur']['login'] == 'admin') {
        $isAdmin = true;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ElectroStore</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* Couleurs principales basées sur des tons de gris */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .navbar {
            background-color: #343a40; /* Gris foncé */
        }

        .navbar-nav .nav-link {
            color: #dcdcdc; /* Gris très clair */
        }

        .navbar-nav .nav-link:hover {
            color: #ffffff; /* Gris très clair sur hover */
        }

        .navbar-brand {
            color: #f8f9fa; /* Blanc cassé */
        }

        .btn-outline-light {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-light:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .container {
            padding-top: 60px;
            padding-bottom: 60px;
        }

        h1, h2, h3 {
            color: #343a40; /* Gris foncé pour les titres */
        }

        .lead {
            color: #6c757d; /* Gris plus clair pour le texte d'introduction */
        }

        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            background-color: #ffffff; /* Blanc pour les cartes */
        }

        .card-img-top {
            border-radius: 8px;
            height: 200px;
            object-fit: cover;
        }

        .footer {
            background-color: #343a40;
            color: #dcdcdc;
            padding: 30px 0;
            text-align: center;
        }

        .footer a {
            color: #dcdcdc;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-primary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
    </style>
</head>
<body>

<!-- Inclure la barre de navigation -->
<?php include 'include/nav.php'; ?>

<!-- Contenu principal -->
<div class="container text-center">
    <h1 class="display-4 fw-bold">Bienvenue sur ElectroStore</h1>
    <p class="lead mb-5">Votre destination incontournable pour les produits électroniques de qualité à des prix compétitifs. Explorez nos catégories et trouvez vos produits préférés dès maintenant.</p>
    <a href="front/" class="btn btn-primary btn-lg shadow-sm px-4 py-2">Explorer nos produits</a>

    <!-- Section des catégories de produits -->
    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://cdn.mos.cms.futurecdn.net/CtE3WTrL8UJiKT7GkaighQ-1200-80.jpg" class="card-img-top" alt="Smartphones">
                <div class="card-body">
                    <h5 class="card-title">Smartphones</h5>
                    <p class="card-text">Des smartphones dernier cri, adaptés à tous les besoins et budgets.</p>
                    <a href="front/" class="btn btn-primary btn-lg shadow-sm px-4 py-2">Voir les smartphones</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://i.redd.it/msl53vqmf4xb1.jpg" class="card-img-top" alt="Ordinateurs portables">
                <div class="card-body">
                    <h5 class="card-title">Ordinateurs Portables</h5>
                    <p class="card-text">Nos ordinateurs portables allient performance et design pour votre quotidien.</p>
                    <a href="front/" class="btn btn-primary btn-lg shadow-sm px-4 py-2">Voir les ordinateurs</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://media.gqmagazine.fr/photos/65fc6fb844552711d02c61e4/master/pass/meilleur-Casque-Audio.jpg" class="card-img-top" alt="Casques et écouteurs">
                <div class="card-body">
                    <h5 class="card-title">Casques et Écouteurs</h5>
                    <p class="card-text">Profitez d'une expérience sonore de qualité supérieure avec nos casques et écouteurs.</p>
                    <a href="front/" class="btn btn-primary btn-lg shadow-sm px-4 py-2">Voir les casques</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2024 ElectroStore. Tous droits réservés.</p>
    <p><a href="privacy-policy.php">Politique de confidentialité</a> | <a href="terms.php">Conditions d'utilisation</a></p>
</footer>

<!-- Scripts Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
