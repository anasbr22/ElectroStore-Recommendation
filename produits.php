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
    <?php include 'include/head.php' ?>
    <title>Liste des produits</title>
    <!-- Ajout d'un CDN Bootstrap minimaliste -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ajout de FontAwesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin-top: 40px;
        }

        h2 {
            color: #333;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 8px 20px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            color: white;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .table img {
            max-height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 2px solid #eee;
        }

        .card {
            border-radius: 10px;
            border: none;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            background-color: transparent;
            text-align: center;
            padding: 1rem;
        }

        .btn-danger {
            background-color: #e74c3c;
            border-radius: 30px;
            color: white;
            font-weight: bold;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .btn-custom {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php' ?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Liste des produits</h2>
                <?php if ($isAdmin): ?>
                    <a href="ajouter_produit.php" class="btn btn-custom float-end"><i class="fas fa-plus"></i> Ajouter un produit</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Libellé</th>
                            <th>Prix</th>
                            <th>Discount</th>
                            <th>Catégorie</th>
                            <th>Date de création</th>
                            <th>Image</th>
                            <th>Opérations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once 'include/database.php';
                        $categories = $pdo->query("SELECT produit.*,categorie.libelle as 'categorie_libelle' FROM produit INNER JOIN categorie ON produit.id_categorie = categorie.id")->fetchAll(PDO::FETCH_OBJ);
                        foreach ($categories as $produit) {
                            $prix = $produit->prix;
                            $discount = $produit->discount;
                            $prixFinale = $prix - (($prix * $discount) / 100);
                        ?>
                            <tr>
                                <td><?= $produit->id ?></td>
                                <td><?= htmlspecialchars($produit->libelle) ?></td>
                                <td><?= number_format($prix, 2, ',', ' ') ?> <i class="fa fa-dollar-sign"></i></td>
                                <td><?= $discount ?> %</td>
                                <td><?= htmlspecialchars($produit->categorie_libelle) ?></td>
                                <td><?= date('d/m/Y', strtotime($produit->date_creation)) ?></td>
                                <td><img class="img-fluid" src="<?= htmlspecialchars($produit->image) ?>" alt="<?= htmlspecialchars($produit->libelle) ?>"></td>
                                <td>
                                    <?php if ($isAdmin): ?>
                                        <a class="btn btn-warning btn-custom" href="modifier_produit.php?id=<?= $produit->id ?>"><i class="fas fa-edit"></i> Modifier</a>
                                        <a class="btn btn-danger btn-custom" href="supprimer_produit.php?id=<?= $produit->id ?>" onclick="return confirm('Voulez-vous vraiment supprimer le produit <?= htmlspecialchars($produit->libelle) ?> ?')"><i class="fas fa-trash-alt"></i> Supprimer</a>
                                    <?php else: ?>
                                        <span class="text-muted">Opérations désactivées</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <p class="text-muted">&copy; <?= date('Y') ?> MonSite.com</p>
            </div>
        </div>
    </div>

    <!-- Ajout de scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
