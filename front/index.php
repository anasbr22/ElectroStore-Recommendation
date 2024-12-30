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

    <?php include '../include/head_front.php' ?>
    <title>Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Header */
        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-top: 20px;
        }

        /* Sidebar */
        .list-group-item {
            border-radius: 8px;
            margin-bottom: 12px;
            background-color: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .list-group-item:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .list-group-item.active {
            background-color: #212A3E;
            color: white;
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
        }

        .list-group-item a {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .list-group-item i {
            margin-right: 10px;
        }

        .list-group-item a:hover {
            color: #0056b3;
            border-left: 3px solid #007bff;
        }

        /* Product Cards */
        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .product-image {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }

        .product-price {
            color: #28a745;
            font-size: 1.1rem;
            margin-top: 5px;
        }

        /* Sticky Sidebar */
        .position-sticky {
            position: sticky;
            top: 20px;
        }

        /* Conteneur de la carte */
        .produit-card {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            /* Ajout de bords arrondis pour une meilleure présentation */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Ajout d'une légère ombre autour de la carte */
            transition: transform 0.3s ease;
            /* Pour un effet de survol agréable */
        }

        /* Effet de flou sur l'image et l'agrandissement */
        .produit-card:hover img {
            filter: blur(8px);
            transform: scale(1.1);
        }

        /* Flou sur le texte du produit au survol */
        .produit-card:hover .card-title,
        .produit-card:hover .card-text {
            filter: blur(3px);
            /* Appliquer un flou au titre et à la description */
            transition: filter 0.3s ease;
        }

        /* Footer centré et caché par défaut */
        .card-footer {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            visibility: hidden;
            opacity: 0;
            background-color: rgba(0, 0, 0, 0.7);
            /* Un fond plus sombre pour un meilleur contraste */
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1;
            color: white;
            font-weight: bold;
            /* Texte en gras pour plus de visibilité */
            max-width: 90%;
            /* Limiter la taille du footer pour ne pas surcharger l'écran */
        }

        /* Footer visible lors du survol */
        .produit-card:hover .card-footer {
            visibility: visible;
            opacity: 1;
        }

        /* Style du lien dans le footer */
        .card-footer a {
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
            display: inline-block;
            padding: 15px 25px;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            /* Un petit espace au-dessus du bouton */
        }

        .card-footer a:hover {
            background-color: #0056b3;
            /* Changement de couleur au survol */
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.7);
            /* Ombre du bouton pour plus de dynamisme */
        }

        /* Améliorer l'apparence du titre et du texte */
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            transition: filter 0.3s ease;
            /* Appliquer un flou au survol */
        }

        /* Effet d'ombre et taille du texte lors du survol */
        .produit-card:hover .card-title {
            filter: blur(3px);
            font-size: 1.6rem;
            /* Augmenter la taille du texte */
            color: #f8f9fa;
            /* Changer la couleur du texte pour contraster */
        }

        /* Amélioration de la description */
        .card-text {
            font-size: 1rem;
            color: #666;
            transition: filter 0.3s ease;
        }

        .produit-card:hover .card-text {
            filter: blur(3px);
            color: #f8f9fa;
        }



        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .col-md-3 {
                width: 100%;
                margin-bottom: 20px;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-price {
                font-size: 0.9rem;
            }

            .list-group-item a {
                font-size: 0.95rem;
            }
        }





        /* ****** */
        form.d-flex {
            max-width: 600px;
            margin: 0 auto;
        }

        form.d-flex .form-control {
            border-radius: 20px;
            padding: 10px 15px;
        }

        form.d-flex .btn {
            border-radius: 20px;
        }

        /* Style du loader */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
            /* Par défaut, le loader est caché */
        }

        .loader .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        /* Animation du spinner */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<div id="loader" class="loader">
    <div class="spinner"></div>
</div>
<script>document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('loader');
        const body = document.querySelector('body');

        // Afficher le loader au chargement de la page
        loader.style.display = 'flex';

        // Cacher le loader une fois que le contenu est chargé
        window.onload = function () {
            loader.style.display = 'none';
        }
    });
</script>

<body>
    <?php include '../include/nav_front.php' ?>
    <div class="container-fluid w-100 py-4 px-4 mx-0">
        <?php
        require_once '../include/database.php';
        $categoryId = isset($_GET['id']) ? (int) $_GET['id'] : NULL;
        $categories = $pdo->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_OBJ);

        if (!is_null(value: $categoryId)) {
            $sqlState = $pdo->prepare("SELECT * FROM produit WHERE id_categorie=? ORDER BY date_creation DESC");
            $sqlState->execute([$categoryId]);
            $produits = $sqlState->fetchAll(PDO::FETCH_OBJ);
        } else {
            $produits = $pdo->query("SELECT * FROM produit ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_OBJ);
        }
        ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar for categories -->
                <div class="navbar-container">
                    <div class="list-group list-group-flush position-sticky sticky-top"
                        style="background-color: #28334A; border-radius: 8px; padding: 15px;">
                        <h4 class="text-center text-light mb-3">
                            <button class="btn btn-outline-light w-100" id="toggle-categories">
                                <i class="fa fa-list"></i> <span id="toggle-text">Liste des catégories</span>
                            </button>
                        </h4>

                        <div id="category-list">
                            <!-- Lien pour tous les produits -->
                            <li class="list-group-item <?= $categoryId == NULL ? 'active' : '' ?>"
                                style="background-color: transparent; border: none;">
                                <a class="btn text-light d-flex align-items-center <?= $categoryId == NULL ? 'fw-bold' : '' ?>"
                                    href="./" aria-label="Voir tous les produits"
                                    style="background-color: #1E2B3E; border-radius: 5px;">
                                    <i class="fa fa-border-all me-2"></i>
                                    <span class="category-text">Voir tous les produits</span>
                                </a>
                            </li>

                            <!-- Liste des catégories -->
                            <?php foreach ($categories as $categorie): ?>
                                <li class="list-group-item <?= $categoryId === $categorie->id ? 'active' : '' ?>"
                                    style="background-color: transparent; border: none;">
                                    <a class="btn text-light d-flex align-items-center <?= $categoryId === $categorie->id ? 'fw-bold' : '' ?>"
                                        href="index.php?id=<?= $categorie->id ?>"
                                        aria-label="Voir les produits de la catégorie <?= $categorie->libelle ?>"
                                        style="background-color: <?= $categoryId === $categorie->id ? '#1E2B3E' : '#324660'; ?>; border-radius: 5px;">
                                        <i class="fa <?= $categorie->icone ?> me-2"></i>
                                        <span class="category-text"><?= $categorie->libelle ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- CSS -->
                <style>
                    .navbar-container {
                        transition: width 0.5s ease;
                        width: 340px;
                        /* Largeur par défaut pour les catégories visibles */
                        min-width: 150px;
                        /* Largeur minimale */


                    }

                    .list-group {
                        box-shadow: 0px 4px 7px black;
                    }

                    .navbar-container.hidden {
                        width: 60px;
                        /* Largeur réduite pour ne montrer que les icônes */
                    }

                    #category-list .category-text {
                        display: inline-block;
                        transition: transform 0.5s ease, opacity 0.5s ease;
                        white-space: nowrap;
                    }

                    #category-list.hidden .category-text {
                        transform: translateX(-100%);
                        opacity: 0;
                    }

                    #category-list .btn {
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                    }

                    #category-list i {
                        font-size: 1.5rem;
                        /* Taille d'icône ajustée pour correspondre au mode caché */
                        transition: color 0.3s ease;
                    }

                    #category-list.hidden i {
                        color: #adb5bd;
                        /* Couleur plus claire pour les icônes en mode caché */
                    }

                    /* Prévenir le débordement de texte */
                    .list-group-item a {
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                </style>



                <!-- JavaScript for toggle functionality -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const toggleButton = document.getElementById('toggle-categories');
                        const categoryList = document.getElementById('category-list');
                        const navbarContainer = document.querySelector('.navbar-container');

                        toggleButton.addEventListener('click', function () {
                            // Toggle the "hidden" class for the animation
                            categoryList.classList.toggle('hidden');
                            navbarContainer.classList.toggle('hidden');

                            // Update button text based on the state
                            const toggleText = document.getElementById('toggle-text');
                            toggleText.textContent = categoryList.classList.contains('hidden')
                                ? ''
                                : 'Liste des catégories';
                        });
                    });


                </script>


                <!-- Products display area -->
                <div class="col mt-4">
                    <div class="row">
                        <?php require_once '../include/front/product/afficher_product.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>