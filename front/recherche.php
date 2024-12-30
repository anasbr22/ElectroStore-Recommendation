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


<?php
// Inclure la connexion à la base de données
require_once '../include/database.php'; 

// Initialiser les variables
$produits = []; 

// Vérifier si le formulaire a été soumis
if (isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);

    // Préparer la requête
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE libelle LIKE :search");
    $stmt->execute(['search' => '%' . $search . '%']);

    // Récupérer les résultats
    $produits = $stmt->fetchAll(PDO::FETCH_OBJ); // Utiliser FETCH_OBJ pour être compatible avec afficher_product.php
    

}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include '../include/head_front.php'; ?>
    <title>Recherche : <?= htmlspecialchars($search ?? '') ?></title>
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
    margin: 0 auto;
    width: 100%;  /* Assure que le formulaire prend toute la largeur disponible */
}

form.d-flex .form-control {
    border-radius: 20px;
    padding: 10px 15px;
    width: 90%;  /* Le champ de recherche prend 90% de la largeur du formulaire */
    margin-right: 10px; /* Un petit espace entre l'input et le bouton */
}

form.d-flex .btn {
    border-radius: 20px;
}

    </style>
    
    
</head>
<body>
    <?php include '../include/nav_front.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Résultats de recherche pour : <?= htmlspecialchars($search ?? '') ?></h1>
        
        <div class="row">
            <?php if (!empty($produits)): ?>
                <?php include '../include/front/product/afficher_product.php'; ?>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    Aucun produit ne correspond à votre recherche.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
