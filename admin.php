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

// Récupérer les données des 15 produits les plus recommandés
// Récupérer les données des 15 produits les plus recommandés
$query = "
    SELECT 
        r.product_id, 
        COUNT(r.user_id) AS recommandation_count, 
        p.libelle AS product_name, 
        p.image AS product_image,
        p.description AS product_description
    FROM recommendations r
    JOIN tout_produit p ON r.product_id = p.id
    GROUP BY r.product_id 
    ORDER BY recommandation_count DESC 
    LIMIT 15
";
$stmt = $pdo->query($query);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérification du nombre de produits récupérés
if (count($produits) != 15) {
    echo "Nombre de produits récupérés : " . count($produits);  // Afficher un message pour déboguer
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
        'in_stock' => inStock($produit['product_id'])
    ];
}

// Fonction pour vérifier si le produit est en stock dans la table `produit` ou `tout_produit`
function inStock($productId)
{
    global $pdo;
    // Vérifier dans la table `produit`
    $query = "SELECT COUNT(*) FROM produit WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$productId]);
    if ($stmt->fetchColumn() > 0) {
        return "Disponible en stock";
    } else {

        return "Produit non disponible, Demander au responsable pour ajouter au stock";
    }

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
    </style>
</head>

<body id="page-top">

    <?php include 'include/nav.php'; ?>

    <?php if ($isAdmin) { ?>
        <div id="wrapper">
            <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">

                <li class="nav-item active"><a class="nav-link" href="admin.php"><i
                            class="fa fa-box"></i><span>Recommandation Produits</span></a></li>
                <li class="nav-item"><a class="nav-link" href="InventoryManagementAgent.php"><i
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
                                    <li class="list-group-item border-0 shadow-sm mb-3">
                                        <div class="d-flex align-items-center">
                                            <!-- ID du produit -->
                                            <div class="flex-grow-1">
                                                <p class="mb-0" style="font-size: 0.9em; color: #6c757d;">ID :
                                                    <?php echo htmlspecialchars($product['id']); ?>
                                                </p>
                                            </div>

                                            <div class="d-flex w-100">
                                                <!-- Image du produit -->
                                                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                                    alt="Image du produit" class="rounded border"
                                                    style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">

                                                <!-- Informations du produit -->
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 text-dark">
                                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                    </h6>
                                                    <p class="mb-2 text-muted" style="font-size: 0.9em;">
                                                        <?php echo htmlspecialchars($product['description']); ?>
                                                    </p>
                                                    <span
                                                        class="badge <?php echo ($product['in_stock'] == "Disponible en stock") ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo htmlspecialchars($product['in_stock']); ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Icône de statut -->
                                            <div>
                                                <?php if ($product['in_stock'] == "Disponible en stock"): ?>
                                                    <i class="fa fa-check-circle text-success" style="font-size: 1.5em;"></i>
                                                <?php else: ?>
                                                    <i class="fa fa-times-circle text-danger" style="font-size: 1.5em;"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <script>
            var ctx2 = document.getElementById('productRecommendationChart').getContext('2d');
            var productRecommendationChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>, // Utilisation de l'ID pour l'axe X
                    datasets: [{
                        label: 'Recommandations de Produits',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'ID du Produit'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de Recommandations'
                            },
                            ticks: {
                                stepSize: 10
                            }
                        }
                    }
                }
            });
        </script>

    <?php } else { ?>
        <div class="container py-5">
            <div class="card shadow-sm rounded-lg">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <i class="fa-solid fa-hand-wave fa-3x text-primary"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold">Bonjour,
                        <?php echo htmlspecialchars($_SESSION['utilisateur']['login']); ?>!
                    </h3>
                    <p class="card-text text-muted">Bienvenue sur votre tableau de bord. Nous sommes heureux de vous voir.
                    </p>
                </div>
            </div>
        </div>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/js/sb-admin-2.min.js"></script>
</body>

</html>