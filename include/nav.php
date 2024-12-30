<?php
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

<head>
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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand fs-4 fw-bold text-light" href="#">
            <i class="fa-solid fa-store"></i> Electrostore
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php $currentPage = $_SERVER['PHP_SELF']; ?>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php if ($currentPage == '/ecommerce/index.php') echo 'active' ?>" href="index.php">
                        <i class="fa-solid fa-home"></i> Accueil
                    </a>
                </li>
                <?php if (!$connecte) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($currentPage == '/ecommerce/ajouter_utilisateur.php') echo 'active' ?>" href="ajouter_utilisateur.php">
                            <i class="fa-solid fa-user-plus"></i> Créer un compte
                        </a>
                    </li>
                <?php } ?>

                <?php if ($connecte) { ?>
                    <?php if ($isAdmin) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($currentPage == '/ecommerce/categories.php') echo 'active' ?>" href="categories.php">
                                <i class="fa-brands fa-dropbox"></i> Liste des catégories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($currentPage == '/ecommerce/produits.php') echo 'active' ?>" href="produits.php">
                                <i class="fa-solid fa-tag"></i> Liste des produits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($currentPage == '/ecommerce/commandes.php') echo 'active' ?>" href="commandes.php">
                                <i class="fa-solid fa-barcode"></i> Commandes
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link <?php if ($currentPage == '/ecommerce/admin.php') echo 'active' ?>" href="admin.php">
                            <i class="fas fa-user-lock"></i> <b><u>Espace admin</u></b> 
                            </a>
                        </li>

                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="deconnexion.php">
                            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                        </a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($currentPage == '/ecommerce/connexion.php') echo 'active' ?>" href="connexion.php">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Connexion
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <a class="btn btn-outline-light ms-3 shadow" href="front/">
            <i class="fa-solid fa-cart-shopping"></i> Acheter maintenant
        </a>
    </div>
</nav>
