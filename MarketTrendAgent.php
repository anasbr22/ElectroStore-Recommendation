<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$connecte = false;
$isAdmin = false;

// Vérifier si l'utilisateur est admin
if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
    $connecte = true;
    if (isset($_SESSION['utilisateur']['login']) && $_SESSION['utilisateur']['login'] == 'admin') {
        $isAdmin = true;
    }
}

// Charger les données CSV
$csvFile = 'API/trends_product_dataset.csv'; // Remplacez par le chemin de votre fichier
$data = [];
if (($handle = fopen($csvFile, "r")) !== false) {
    $headers = fgetcsv($handle); // Lire l'en-tête
    while (($row = fgetcsv($handle)) !== false) {
        $data[] = array_combine($headers, $row);
    }
    fclose($handle);
}

// Filtrer les catégories Electronics et Mobile phone accessories
$filteredData = array_filter($data, function ($product) {
    return in_array($product['Category'], ['Electronics', 'Mobile phone accessories']);
});

// Calculer les statistiques
$totalSales = 0;
$totalProducts = count($filteredData);
$totalRatings = 0;
$totalPrice = 0;
$bestSellingProduct = null;
$highestRatedProduct = null;

foreach ($filteredData as $product) {
    $totalSales += $product['Sales'];
    $totalRatings += $product['Rating'];
    $totalPrice += $product['Price'];

    // Meilleure vente
    if (!$bestSellingProduct || $product['Sales'] > $bestSellingProduct['Sales']) {
        $bestSellingProduct = $product;
    }

    // Meilleur rating
    if (!$highestRatedProduct || $product['Rating'] > $highestRatedProduct['Rating']) {
        $highestRatedProduct = $product;
    }
}

// Moyennes
$averagePrice = $totalPrice / $totalProducts;
$averageRating = $totalRatings / $totalProducts;

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

                <li class="nav-item "><a class="nav-link" href="InventoryManagementAgent.php"><i
                            class="fa fa-cogs"></i><span>Gestion
                            des Stocks</span></a></li>
                <li class="nav-item active"><a class="nav-link" href="MarketTrendAgent.php"><i
                            class="fa fa-chart-line"></i><span>Tendances du Marché</span></a></li>

            </ul>





            <div id="content-wrapper" class="d-flex flex-column">

                <div class="container my-5">
                    <h1 class="text-center">Analyse des Produits Électroniques</h1>
                    <div class="row">
                        <!-- Total des Ventes -->
                        <div class="col-md-4">
                            <div class="card shadow-sm text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Total des Ventes</h5>
                                    <p class="card-text"><strong><?= $totalSales; ?></strong></p>
                                </div>
                            </div>
                        </div>
                        <!-- Prix Moyen -->
                        <div class="col-md-4">
                            <div class="card shadow-sm text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Prix Moyen</h5>
                                    <p class="card-text"><strong><?= number_format($averagePrice, 2); ?> €</strong></p>
                                </div>
                            </div>
                        </div>
                        <!-- Note Moyenne -->
                        <div class="col-md-4">
                            <div class="card shadow-sm text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Note Moyenne</h5>
                                    <p class="card-text"><strong><?= number_format($averageRating, 1); ?> / 5</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meilleures Performances -->
                    <div class="row my-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Produit le Plus Vendu</h5>
                                    <p class="card-text">
                                        <strong>Nom :</strong> <?= $bestSellingProduct['ProductName']; ?><br>
                                        <strong>Ventes :</strong> <?= $bestSellingProduct['Sales']; ?><br>
                                        <strong>Catégorie :</strong> <?= $bestSellingProduct['Category']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Produit avec la Meilleure Note</h5>
                                    <p class="card-text">
                                        <strong>Nom :</strong> <?= $highestRatedProduct['ProductName']; ?><br>
                                        <strong>Note :</strong> <?= $highestRatedProduct['Rating']; ?> / 5<br>
                                        <strong>Catégorie :</strong> <?= $highestRatedProduct['Category']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique des Ventes par Catégorie -->
                    <canvas id="salesChart"></canvas>


                    <?php
                    // Trier les produits par ventes (descending)
                    usort($filteredData, function ($a, $b) {
                        return $b['Sales'] - $a['Sales'];
                    });

                    // Obtenir les 20 premiers produits
                    $topTrendingProducts = array_slice($filteredData, 0, 20);
                    ?>

                    <div class="container my-5">
                        <h2 class="text-center">Top 20 Produits Électroniques Tendance</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nom du Produit</th>
                                        <th>Catégorie</th>
                                        <th>Ventes</th>
                                        <th>Note</th>
                                        <th>Prix (€)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topTrendingProducts as $index => $product) { ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= htmlspecialchars($product['ProductName']); ?></td>
                                            <td><?= htmlspecialchars($product['Category']); ?></td>
                                            <td><?= $product['Sales']; ?></td>
                                            <td><?= number_format($product['Rating'], 1); ?> / 5</td>
                                            <td><?= number_format($product['Price'], 2); ?> €</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>





        <!-- Ajouter Font Awesome pour les icônes -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">











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


    <script>
        // Données pour le graphique
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($filteredData, 'ProductName')); ?>,
                datasets: [{
                    label: 'Ventes',
                    data: <?= json_encode(array_column($filteredData, 'Sales')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    </script>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</html>