<?php
session_start();

// Vérifier si l'utilisateur est connecté avant d'afficher son nom
if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$connecte = false;
$isAdmin = false;

// Vérifier si l'utilisateur est connecté et admin
if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
    $connecte = true;

    if (isset($_SESSION['utilisateur']['login']) && $_SESSION['utilisateur']['login'] == 'admin') {
        $isAdmin = true;
    }
}

// Connexion à la base de données (Assurez-vous que vos paramètres sont corrects)
$pdo = new PDO('mysql:host=localhost;dbname=ecommerce', 'root', '');

// Récupérer les données des produits recommandés***************************************************************
$query = "
        SELECT 
        r.product_id, 
        COUNT(r.user_id) AS recommandation_count, 
        p.libelle AS product_name, 
        p.image AS product_image,  
        p.description AS product_description, 
        p.quantite
    FROM recommendations r
    JOIN produit p ON r.product_id = p.id
    LEFT JOIN tout_produit ps ON p.id = ps.id
     WHERE p.quantite < 20
    GROUP BY r.product_id 
    ORDER BY recommandation_count DESC 
    LIMIT 30
";
$stmt = $pdo->query($query);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);




// Vérification du nombre de produits récupérés
if (count($produits) != 15) {
    // echo "Nombre de produits récupérés : " . count($produits);  // Afficher un message pour déboguer
}

// Préparer les labels et les données pour le graphique
$labels = [];
$data = [];
$productDetails = [];

foreach ($produits as $produit) {
    $labels[] = $produit['product_id']; // Utilisation de l'ID du produit pour l'axe X
    $data[] = $produit['recommandation_count'];
    $productDetails[] = [
        'id' => $produit['product_id'],
        'name' => $produit['product_name'],
        'image' => $produit['product_image'],
        'description' => $produit['product_description'],
        'stock_quantity' => $produit['quantite'],
        'in_stock' => checkStock($produit['quantite'])
    ];
}

// Fonction pour vérifier la disponibilité du produit en stock et générer des alertes
function checkStock($stockQuantity)
{
    if ($stockQuantity < 20) {
        return "Quantité faible (moins de 20), recommander un réapprovisionnement!";
    }
    return "Disponible en stock";
}

?>

<!doctype html>
<html lang="en">

<head>
    <?php include 'include/head.php'; ?>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .navbar {
            background-color: #4e73df;
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        .card-body {
            font-size: 16px;
        }

        .product-list {
            margin-top: 30px;
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: box-shadow 0.3s ease;
        }

        .product-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .product-item img {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            margin-right: 15px;
        }

        .product-item .details {
            flex-grow: 1;
        }
    </style>
</head>

<body id="page-top">

    <?php include 'include/nav.php'; ?>

    <?php if ($isAdmin) { ?>
        <div id="wrapper">
            <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fa fa-box"></i><span>Recommandation
                            Produits</span></a></li>

                <li class="nav-item active"><a class="nav-link" href="InventoryManagementAgent.php"><i
                            class="fa fa-cogs"></i><span>Gestion
                            des Stocks</span></a></li>
                <li class="nav-item"><a class="nav-link" href="MarketTrendAgent.php"><i
                            class="fa fa-chart-line"></i><span>Tendances du Marché</span></a></li>

            </ul>





            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <div class="container-fluid">
                        <h3 class="text-center text-light">Dashboard Admin</h3>

                        <div class="col">
                            <center>
                                <div class="col-lg-11">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Recommandations de Produits</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="productRecommendationChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </center>
                        </div>

                        <div class="product-list mt-4">
                            <h5 class="text-primary mb-4"><i class="fa fa-star"></i> Produits Recommandés</h5>
                            <ul class="list-group">
                                <?php foreach ($productDetails as $product): ?>
                                    <li class="product-item">
                                        <!-- ID du produit -->
                                        <div class="details">
                                            <p class="mb-0" style="font-size: 0.9em; color: #6c757d;">ID :
                                                <?php echo htmlspecialchars($product['id']); ?>
                                            </p>
                                            <h6 class="mb-1 text-dark">
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            </h6>
                                            <p class="mb-2 text-muted" style="font-size: 0.9em;">
                                                <?php echo htmlspecialchars($product['description']); ?>
                                            </p>
                                            <span
                                                class="badge <?php echo ($product['in_stock'] == 'Disponible en stock') ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo htmlspecialchars($product['in_stock']); ?>
                                            </span>
                                        </div>

                                        <!-- Image du produit -->
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Image du produit">

                                        <!-- Icône de statut -->
                                        <div>
                                            <?php if ($product['in_stock'] == "Disponible en stock"): ?>
                                                <i class="fa fa-check-circle text-success" style="font-size: 1.5em;"></i>
                                            <?php else: ?>
                                                <i class="fa fa-exclamation-circle text-warning" style="font-size: 1.5em;"></i>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php

        // Analyse complète des stocks
        $queryStock = "
        SELECT 
            COUNT(*) AS total_produits,
            SUM(quantite) AS total_quantite,
            SUM(quantite * prix) AS valeur_totale_stock,
            SUM(CASE WHEN quantite < 20 THEN 1 ELSE 0 END) AS produits_faible_stock,
            SUM(CASE WHEN quantite = 0 THEN 1 ELSE 0 END) AS produits_epuise
        FROM produit
        ";

        $stockStats = $pdo->query($queryStock)->fetch(PDO::FETCH_ASSOC);

        // Identifier les produits à promouvoir
        $queryPromotions = "
SELECT 
    id, libelle, prix, discount, quantite, date_creation,
    DATEDIFF(NOW(), date_creation) AS anciennete,
    ratings, no_of_ratings
FROM produit
WHERE quantite > 50 OR (ratings < 3 AND no_of_ratings > 10)
ORDER BY anciennete DESC, quantite DESC
";
        $produitsPromotions = $pdo->query($queryPromotions)->fetchAll(PDO::FETCH_ASSOC);

        // Proposer des promotions
        $promotionsProposees = [];
        foreach ($produitsPromotions as $produit) {
            $nouveauDiscount = 0;

            // Logique de promotion
            if ($produit['quantite'] > 100) {
                $nouveauDiscount = 20; // 20% de réduction pour les gros stocks
            } elseif ($produit['anciennete'] > 365) {
                $nouveauDiscount = 15; // 15% de réduction pour les produits anciens
            } elseif ($produit['ratings'] < 3) {
                $nouveauDiscount = 10; // 10% de réduction pour les produits mal notés
            }

            if ($nouveauDiscount > $produit['discount']) {
                $promotionsProposees[] = [
                    'id' => $produit['id'],
                    'libelle' => $produit['libelle'],
                    'ancien_discount' => $produit['discount'],
                    'nouveau_discount' => $nouveauDiscount,
                ];
            }
        }
        ?>


        <div class="container mt-5">
            <h3 class="text-center text-primary fw-bold">Analyse Complète des Stocks</h3>

            <!-- Statistiques Globales -->
            <div class="row my-4">
                <div class="col-md-4">
                    <div class="card shadow-lg border-primary">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">Total Produits</h5>
                            <p class="text-primary fs-3">
                                <i class="fas fa-boxes"></i>
                                <?php echo $stockStats['total_produits']; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-lg border-success">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">Valeur Totale des Stocks</h5>
                            <p class="text-success fs-3">
                                <i class="fas fa-dollar-sign"></i>
                                <?php echo number_format($stockStats['valeur_totale_stock'], 2); ?> USD
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-lg border-danger">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">Produits Épuisés</h5>
                            <p class="text-danger fs-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo $stockStats['produits_epuise']; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotions Proposées -->
            <div class="mt-5">
                <h4 class="text-primary fw-bold">Promotions Proposées</h4>
                <table class="table table-bordered table-hover mt-3 align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>ID Produit</th>
                            <th>Libellé</th>
                            <th>Ancien Discount (%)</th>
                            <th>Nouveau Discount (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promotionsProposees as $promo): ?>
                            <tr class="text-center">
                                <td><?php echo htmlspecialchars($promo['id']); ?></td>
                                <td><?php echo htmlspecialchars($promo['libelle']); ?></td>
                                <td>
                                    <span
                                        class="badge bg-secondary"><?php echo htmlspecialchars($promo['ancien_discount']); ?>%</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-success"><?php echo htmlspecialchars($promo['nouveau_discount']); ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ajouter Font Awesome pour les icônes -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">









        <script>
            var ctx2 = document.getElementById('productRecommendationChart').getContext('2d');
            var productRecommendationChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Recommandations de Produits',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        </script>

    <?php } else { ?>
        <div class="container py-5">
            <div class="card shadow-sm rounded-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <h4 class="text-danger">Accès interdit</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</html>