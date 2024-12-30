<?php
session_start(); // Démarrer la session
require_once 'include/database.php';
$idCommande = $_GET['id'];

// Récupérer les détails de la commande
$sqlState = $pdo->prepare('SELECT commande.*, utilisateur.login as "login" FROM commande
            INNER JOIN utilisateur ON commande.id_client = utilisateur.id
            WHERE commande.id = ? ORDER BY commande.date_creation DESC');
$sqlState->execute([$idCommande]);
$commande = $sqlState->fetch(PDO::FETCH_ASSOC);

// Vérifier si la commande existe et que la requête a réussi
if ($commande === false) {
    echo "Erreur : Commande introuvable.";
    exit();
}

// Vérifier si la commande est validée ou non
$estValide = $commande['valide'] == 1;  // 1 = validée, 0 = en attente
?>
<!doctype html>
<html lang="en">
<head>
    <?php include 'include/head.php'; ?>
    <title>Commande | Numéro <?= $commande['id'] ?></title>
</head>
<body>
    <?php include 'include/nav.php'; ?>

    <div class="container py-4">
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

        <h2>Détails de la Commande #<?= $commande['id'] ?></h2>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Informations sur la commande</h5>
                <p><strong>Client :</strong> <?= $commande['login'] ?></p>
                <p><strong>Total :</strong> <?= $commande['total'] ?> <i class="fa fa-solid fa-dollar"></i></p>
                <p><strong>Date de création :</strong> <?= $commande['date_creation'] ?></p>
                <p><strong>État :</strong> <?= $estValide ? 'Validée' : 'En attente' ?></p>

                <!-- Bouton de validation/annulation -->
                <?php if (!$estValide): ?>
                    <a href="valider_commande.php?id=<?= $commande['id'] ?>&etat=1"
                       class="btn btn-success btn-lg">
                        Valider la commande
                    </a>
                <?php else: ?>
                    <a href="valider_commande.php?id=<?= $commande['id'] ?>&etat=0"
                       class="btn btn-danger btn-lg">
                        Annuler la commande
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <h3>Produits :</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Produit</th>
                    <th>Prix Unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Récupérer les produits associés à la commande
                    $sqlLignesCommande = $pdo->prepare('SELECT ligne_commande.*, produit.libelle, produit.prix, produit.image 
                                                        FROM ligne_commande
                                                        INNER JOIN produit ON ligne_commande.id_produit = produit.id
                                                        WHERE ligne_commande.id_commande = ?');
                    $sqlLignesCommande->execute([$idCommande]);
                    $lignesCommandes = $sqlLignesCommande->fetchAll(PDO::FETCH_OBJ);

                    foreach ($lignesCommandes as $lignesCommande): ?>
                    <tr>
                        <td><?= $lignesCommande->id ?></td>
                        <td>
                            <img src="<?= $lignesCommande->image ?>" alt="<?= $lignesCommande->libelle ?>" width="50" class="img-fluid">
                            <?= $lignesCommande->libelle ?>
                        </td>
                        <td><?= $lignesCommande->prix ?> <i class="fa fa-solid fa-dollar"></i></td>
                        <td>x <?= $lignesCommande->quantite ?></td>
                        <td><?= $lignesCommande->prix * $lignesCommande->quantite ?> <i class="fa fa-solid fa-dollar"></i></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
