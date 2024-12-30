<?php
ob_start();
session_start();
require_once '../include/database.php';

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;

$user_id = $idUtilisateur; // ID de l'utilisateur connecté
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Validation de l'ID du produit
$sqlState = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
$sqlState->execute([$id]);
$produit = $sqlState->fetch(PDO::FETCH_ASSOC);

if ($produit && $produit['id_categorie'] == 10) {
    // Requête à l'API Flask pour récupérer les produits recommandés
    $product_id = $id;
    $api_url = "http://127.0.0.1:5000/recommend?product_id=" . $product_id; // URL de l'API Flask
    $response = file_get_contents($api_url);

    if ($response) {
        $recommended_products = json_decode($response, true);

        // Vérification de l'utilisateur connecté
        if ($idUtilisateur != 0) {

            // Limiter à 3 produits aléatoires parmi les 20 recommandés
            if (count($recommended_products) > 3) {
                shuffle($recommended_products);
                $recommended_products2 = array_slice($recommended_products, 0, 3);
            }



            foreach ($recommended_products2 as $product) {
                $product_id = intval($product['id']);

                // Vérifier si le produit existe dans la table tout_produit
                $productExistsQuery = $pdo->prepare("SELECT COUNT(*) FROM tout_produit WHERE id = ?");
                $productExistsQuery->execute([$product_id]);
                $productExists = $productExistsQuery->fetchColumn();

                // Vérifier si la recommandation existe déjà pour cet utilisateur
                $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM recommendations WHERE user_id = ? AND product_id = ?");
                $checkQuery->execute([$user_id, $product_id]);
                $exists = $checkQuery->fetchColumn();

                if ($productExists && !$exists) {

                    // Désactiver les triggers temporairement (si vous avez des triggers qui entrent en conflit)
                    $sqlState = $pdo->prepare("SET SESSION SQL_LOG_BIN = 0");
                    $sqlState->execute();
                    // Compter les recommandations existantes pour l'utilisateur
                    $countQuery = $pdo->prepare("SELECT COUNT(*) FROM recommendations WHERE user_id = ?");
                    $countQuery->execute([$user_id]);
                    $count = $countQuery->fetchColumn();

                    if ($count >= 30) {
                        // Supprimer la recommandation la plus ancienne
                        $deleteQuery = $pdo->prepare("DELETE FROM recommendations WHERE user_id = ? ORDER BY id ASC LIMIT 1");
                        $deleteQuery->execute([$user_id]);
                    }

                    // Insérer la nouvelle recommandation
                    $insertQuery = $pdo->prepare("INSERT INTO recommendations (user_id, product_id) VALUES (?, ?)");
                    $insertQuery->execute([$user_id, $product_id]);


                    // Réactiver les triggers
                    $sqlState = $pdo->prepare("SET SESSION SQL_LOG_BIN = 1");
                    $sqlState->execute();
                } elseif (!$productExists) {
                    error_log("Produit non trouvé dans la base de données : " . $product_id);
                }
            }




        }
    }
} else {
    // Si l'id_categorie n'est pas égal à 10, aucune action
    $recommended_products = [];
}
?>

<!doctype html>
<html lang="fr">

<head>
    <?php include '../include/head_front.php' ?>
    <title>
        <?php echo isset($produit['libelle']) ? htmlspecialchars($produit['libelle']) : 'Produit non trouvé'; ?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .not-found {
            text-align: center;
            color: #dc3545;
            margin-top: 20%;
        }

        .product-title {
            color: #343a40;
            font-size: 2rem;
        }

        .badge {
            font-size: 1rem;
        }

        img {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-body {
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            color: #343a40;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-grid .card {
            cursor: pointer;
        }

        .stock {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include '../include/nav_front.php'; ?>

    <div class="container py-4">
        <?php if ($produit): ?>
            <div class="card p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <img class="img-fluid w-75" src="<?php echo htmlspecialchars($produit['image']); ?>"
                            alt="<?php echo htmlspecialchars($produit['libelle']); ?>">
                    </div>
                    <div class="col-md-6">
                        <h1 class="product-title mb-3"><?php echo htmlspecialchars($produit['libelle']); ?></h1>

                        <?php if (!empty($produit['discount'])):
                            $percent = intval(100 - (($produit['discount'] * 100) / $produit['prix'])); // Conversion en entier 
                            ?>
                            <span class="badge bg-success">- <?php echo htmlspecialchars($percent); ?>%</span>
                        <?php endif; ?>
                        <hr>
                        <p class="text-muted">
                            <?php echo nl2br(htmlspecialchars($produit['description'])); ?>
                        </p>
                        <hr>
                        <div class="d-flex align-items-center">
                            <?php
                            $prix = $produit['prix'];
                            if (!empty($produit['discount'])):
                                ?>
                                <h5 class="text-danger me-3">
                                    <strike><?php echo htmlspecialchars($prix); ?> $</strike>
                                </h5>
                                <h5 class="text-success"><?php echo htmlspecialchars($produit['discount']); ?> $</h5>
                            <?php else: ?>
                                <h5 class="text-success"><?php echo htmlspecialchars($prix); ?> $</h5>
                            <?php endif; ?>
                        </div>

                        <hr>
                        <!-- Affichage de la quantité restante -->
                        <?php if (!empty($produit['quantite'])): ?>
                            <p class="stock">Stock restant : <?php echo htmlspecialchars($produit['quantite']); ?> unités</p>
                        <?php endif; ?>

                        <div>
                            <?php $idProduit = $produit['id']; ?>
                            <?php include '../include/front/counter.php'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des produits recommandés -->
            <?php if (isset($recommended_products) && !empty($recommended_products)): ?>
                <div class="mt-5">
                    <h3 class="text-center mb-4 text-shadow"><b>Produits recommandés</b></h3>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        <?php foreach ($recommended_products as $product): ?>
                            <div class="col">
                                <div class="card shadow-sm border-light rounded">
                                    <img src="<?php echo $product['image']; ?>" class="card-img-top"
                                        alt="<?php echo $product['libelle']; ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-truncate"><?php echo $product['libelle']; ?></h5>
                                        <p class="card-text text-muted mb-3"><?php echo $product['prix']; ?> $</p>
                                        <a href="produit.php?id=<?php echo $product['id']; ?>" class="btn btn-primary mt-auto">Voir
                                            le produit</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>

        <?php else: ?>
            <div class="not-found">
                <h1>Produit non trouvé</h1>
                <p>Le produit que vous recherchez n'existe pas ou a été supprimé.</p>
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>