<?php
session_start(); // Démarrer la session

$connecte = false;
$isAdmin = false;
$userId = null; // Initialiser l'ID de l'utilisateur

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
    $connecte = true;

    // Vérifier si l'utilisateur est admin
    if (isset($_SESSION['utilisateur']['login']) && $_SESSION['utilisateur']['login'] == 'admin') {
        $isAdmin = true;
    }

    // Récupérer l'ID de l'utilisateur connecté
    $userId = $_SESSION['utilisateur']['id']; // Utiliser l'ID de l'utilisateur stocké en session
}
?>

<?php
// Inclure la connexion à la base de données
require_once '../include/database.php'; 

// Initialiser les variables
$produitsDisponibles = []; 
$produitsIndisponibles = []; 

// Vérifier si l'utilisateur est connecté
if ($connecte && $userId) {
    // Préparer la requête pour les produits recommandés disponibles pour l'utilisateur courant
    $stmtDisponible = $pdo->prepare("
        SELECT p.* 
        FROM produit p 
        INNER JOIN recommendations r ON p.id = r.product_id
        WHERE r.user_id = :user_id
    ");
    $stmtDisponible->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtDisponible->execute();
    $produitsDisponibles = $stmtDisponible->fetchAll(PDO::FETCH_OBJ);

    // Préparer la requête pour les produits recommandés non disponibles pour l'utilisateur courant
    $stmtIndisponible = $pdo->prepare("
        SELECT t.* 
        FROM tout_produit t 
        INNER JOIN recommendations r ON t.id = r.product_id
        WHERE r.user_id = :user_id
        AND NOT EXISTS (
            SELECT 1 
            FROM produit p 
            WHERE p.id = t.id
        )
    ");
    $stmtIndisponible->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtIndisponible->execute();
    $produitsIndisponibles = $stmtIndisponible->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include '../include/head_front.php'; ?>
    <title>Recommandations Produits</title>
    <!-- Ajouter Bootstrap, AOS et d'autres bibliothèques modernes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Style général */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Titre principal */
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #444;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Cartes de produits */
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff;
            border: none;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            border-bottom: 1px solid #ddd;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
        }

        .product-price {
            color: #28a745;
            font-size: 1.3rem;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-radius: 30px;
            padding: 12px 25px;
            cursor: not-allowed;
        }

        /* Alert */
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-top: 30px;
            font-size: 1.2rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .col-md-3 {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <?php include '../include/nav_front.php'; ?>

    <div class="container mt-5">
        <h1 data-aos="fade-up">Produits Recommandés pour Vous</h1>

        <!-- Bloc des produits disponibles -->
        <div class="product-block">
            <h2>Produits Disponibles</h2>
            <div class="row">
                <?php if (!empty($produitsDisponibles)): ?>
                    <?php foreach ($produitsDisponibles as $produit): ?>
                        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                            <div class="card">
                                <img src="<?= htmlspecialchars($produit->image) ?>" class="product-image" alt="<?= htmlspecialchars($produit->libelle) ?>">
                                <div class="card-body">
                                    <p class="product-title"><?= htmlspecialchars($produit->libelle) ?></p>
                                    <p class="product-price"><?= number_format($produit->prix, 2, ',', ' ') ?> MAD</p>
                                    <a href="produit.php?id=<?= $produit->id ?>" class="btn btn-primary">Voir Détails</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert" role="alert">
                        Aucun produit disponible actuellement. <strong>Nous vous recommandons de revenir plus tard.</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bloc des produits non disponibles -->
        <div class="product-block">
            <h2>Produits Non Disponibles</h2>
            <div class="row">
                <?php if (!empty($produitsIndisponibles)): ?>
                    <?php foreach ($produitsIndisponibles as $produit): ?>
                        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                            <div class="card">
                                <img src="<?= htmlspecialchars($produit->image) ?>" class="product-image" alt="<?= htmlspecialchars($produit->libelle) ?>">
                                <div class="card-body">
                                    <p class="product-title"><?= htmlspecialchars($produit->libelle) ?></p>
                                    <p class="product-price"><?= number_format($produit->prix, 2, ',', ' ') ?> MAD</p>
                                    <button class="btn btn-secondary" disabled>Indisponible</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert" role="alert">
                        Aucun produit non disponible actuellement. <strong>Restez connecté pour les mises à jour!</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Intégration des scripts Bootstrap et AOS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000, // Durée des animations
        });
    </script>
</body>
</html>
