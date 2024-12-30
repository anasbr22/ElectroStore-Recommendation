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
    <?php include 'include/head.php'; ?>
    <title>Liste des Commandes</title>
</head>
<body>
    <?php include 'include/nav.php'; ?>

    <div class="container py-4">
        <h2>Liste des Commandes</h2>

        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Affichage de messages pour l'utilisateur connecté -->
        <?php if ($connecte) : ?>
            <div class="alert alert-info">
                Bonjour, <?= $_SESSION['utilisateur']['login'] ?> ! Vous êtes connecté <?= $isAdmin ? '(Administrateur)' : '(Client)' ?>.
            </div>
        <?php else : ?>
            <div class="alert alert-warning">
                Vous n'êtes pas connecté. Veuillez vous connecter pour accéder à vos commandes.
            </div>
        <?php endif; ?>

        <!-- Tableau des commandes -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Opérations</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once 'include/database.php';
                $commandes = $pdo->query('SELECT commande.*, utilisateur.login as "login" FROM commande
                                          INNER JOIN utilisateur ON commande.id_client = utilisateur.id
                                          ORDER BY commande.date_creation DESC')->fetchAll(PDO::FETCH_ASSOC);
                foreach ($commandes as $commande) :
                ?>
                    <tr>
                        <td><?= $commande['id'] ?></td>
                        <td><?= $commande['login'] ?></td>
                        <td><?= $commande['total'] ?> <i class="fa fa-solid fa-dollar"></i></td>
                        <td><?= $commande['date_creation'] ?></td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="commande.php?id=<?= $commande['id'] ?>">Afficher détails</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
