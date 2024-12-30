
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
<html lang="en">
<head>
    <?php include 'include/head.php' ?>
    <title>Liste des catégories</title>
    <!-- Ajouter une feuille de style personnalisée -->
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn {
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-primary:hover, .btn-danger:hover {
            opacity: 0.8;
        }

        .fa {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>
<?php include 'include/nav.php' ?>

<div class="container py-4">
    <h2>Liste des catégories</h2>
    <a href="ajouter_categorie.php" class="btn btn-primary mb-3">Ajouter catégorie</a>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Libelle</th>
                        <th>Description</th>
                        <th>Icone</th>
                        <th>Date</th>
                        <th>Opérations</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    require_once 'include/database.php';
                    $categories = $pdo->query('SELECT * FROM categorie')->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $categorie) {
                        ?>
                        <tr>
                            <td><?php echo $categorie['id'] ?></td>
                            <td><?php echo $categorie['libelle'] ?></td>
                            <td><?php echo $categorie['description'] ?></td>
                            <td>
                                <i class="fa <?php echo $categorie['icone'] ?>"></i>
                            </td>
                            <td><?php echo $categorie['date_creation'] ?></td>
                            <td>
                                <a href="modifier_categorie.php?id=<?php echo $categorie['id'] ?>" class="btn btn-primary">Modifier</a>
                                <a href="supprimer_categorie.php?id=<?php echo $categorie['id'] ?>"
                                   onclick="return confirm('Voulez-vous vraiment supprimer la catégorie <?php echo $categorie['libelle'] ?> ?');"
                                   class="btn btn-danger">Supprimer</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
