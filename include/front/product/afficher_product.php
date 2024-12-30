<?php
foreach ($produits as $produit) {
    $idProduit = htmlspecialchars($produit->id);
    $libelle = htmlspecialchars($produit->libelle);
    $description = htmlspecialchars($produit->description);
    $image = htmlspecialchars($produit->image);
    $rating = htmlspecialchars($produit->ratings);
    $date_creation = date_format(date_create($produit->date_creation), 'Y/m/d');
    $stock = htmlspecialchars($produit->quantite);  // Supposons que le stock est récupéré depuis la base de données
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 p-6 produit-card">
            <?php if (!empty($produit->discount)): ?>
                <span class="badge rounded-pill text-bg-warning w-25 position-absolute m-2" style="right:0">
                    <?php
                    $percent = 0; // Par défaut
                    if ($produit->discount != 0 && $produit->prix != 0) {
                        $percent = intval(100 - (($produit->discount * 100) / $produit->prix)); // Conversion en entier
                    }
                    ?>
                    - <?= htmlspecialchars($percent) ?> <i class="fa fa-percent"></i>
                </span>
            <?php endif; ?>

            <img class="card-img-top w-50 mx-auto" src="<?= $image ?>" alt="Card image cap">
            <div class="card-body">
                <a href="produit.php?id=<?= $idProduit ?>" class="btn stretched-link"></a>
                <h5 class="card-title"><?= $libelle ?></h5>
                <p class="card-text"><?= $description ?></p>
                <p class="card-text"><small class="text-muted"><?= $date_creation ?> <i class="fa-solid fa-minus"></i>
                        <?= $rating ?><i class="fa-solid fa-star"></i></small></p>
            </div>
            <div class="card-footer rounded d-flex flex-column align-items-center">
                <a href="produit.php?id=<?= $idProduit ?>" class="btn btn-primary btn-lg w-75 mb-3 text-center shadow-sm"
                    style="text-decoration: none;">Voir le produit</a>

                <?php if (!empty($produit->discount)): ?>
                    <!-- Prix d'origine avec remise -->
                    <div class="h5 mb-2">
                        <span class="badge rounded-pill text-bg-danger">
                            <strike><?= htmlspecialchars($produit->prix) ?></strike>
                            <i class="fa fa-solid fa-dollar"></i>
                        </span>
                    </div>
                    <!-- Prix avec remise -->
                    <div class="h5 mb-2">
                        <span class="badge rounded-pill text-bg-success">
                            Solde : <?= $produit->discount ?>
                            <i class="fa fa-solid fa-dollar"></i>
                        </span>
                    </div>
                <?php else: ?>
                    <!-- Prix sans remise -->
                    <div class="h5 mb-2">
                        <span class="badge rounded-pill text-bg-success">
                            <?= htmlspecialchars($produit->prix) ?>
                            <i class="fa fa-solid fa-dollar"></i>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Affichage du compteur restant -->
                <div class="h5 mb-2">
                    <span class="badge rounded-pill text-bg-info">
                        Stock restant : <?= $stock ?>
                    </span>
                </div>

                <!-- Compteur ou autres éléments -->
                <?php include '../include/front/counter.php' ?>
            </div>
        </div>
    </div>
    <?php
}
if (empty($produits)) {
    ?>
    <div class="alert alert-info" role="alert">
        Pas de produits pour l'instant
    </div>
    <?php
}
?>

<style>
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e7e7e7;
    }

    .card-footer .badge {
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .h5 {
        font-size: 1.1rem;
    }

    .shadow-sm {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
