<div>
    <?php
    $idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
    $qty = $_SESSION['panier'][$idUtilisateur][$idProduit] ?? 0;

    // Assurez-vous que $idProduit est défini
    if (!isset($idProduit)) {
        echo "Produit non trouvé.";
        exit;
    }

    // Requête pour récupérer la quantité disponible en stock
    $sql = $pdo->prepare("SELECT quantite FROM produit WHERE id = ?");
    $sql->execute([$idProduit]);
    $produitInfo = $sql->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le produit existe et si la quantité est disponible
    if ($produitInfo) {
        $stockDisponible = $produitInfo['quantite'];
    } else {
        echo "Produit non trouvé.";
        exit;
    }

    // Déterminer la couleur et le texte des boutons selon la disponibilité
    if ($qty == 0) {
        $color = 'btn-outline-primary';  // Couleur simplifiée
        $button = '<i class="fa fa-light fa-cart-plus"></i>';
    } else {
        $button = '<i class="fa-solid fa-pencil"></i>';
    }

    // Vérification si la quantité demandée dépasse le stock disponible
    if ($qty >= $stockDisponible) {
        $disabled = 'disabled'; // Désactiver les boutons si plus de stock
    } else {
        $disabled = ''; // Sinon, les boutons sont activés
    }
    ?>

    <?php if ($idUtilisateur !== 0): ?>
        <form method="post" class="counter d-flex align-items-center justify-content-center" action="ajouter_panier.php">
            <button type="button" class="btn btn-outline-secondary mx-2 counter-moins rounded-circle shadow-sm"
                style="width: 40px; height: 40px; font-size: 1.5rem;" <?= $disabled ?>>-</button>
            <input type="hidden" name="id" value="<?= htmlspecialchars($idProduit) ?>">
            <input class="form-control text-center mx-2" value="<?= htmlspecialchars($qty) ?>" type="number" name="qty"
                id="qty" max="<?= $stockDisponible ?>" style="width: 60px;" <?= $disabled ?>>
            <button type="button" class="btn btn-outline-secondary mx-2 counter-plus rounded-circle shadow-sm"
                style="width: 40px; height: 40px; font-size: 1.5rem;" <?= $disabled ?>>+</button>

            <button class="btn btn-success btn-sm mx-2 shadow-sm" type="submit" name="ajouter" <?= $disabled ?>>
                <?= $button ?>
            </button>
            <?php if ($qty != 0): ?>
                <button formaction="supprimer_panier.php" class="btn btn-sm btn-danger mx-1 shadow-sm" type="submit"
                    name="supprimer">
                    <i class="fa-solid fa-trash"></i>
                </button>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Vous devez être connecté pour acheter ce produit <strong><a href="../connexion.php"
                    class="alert-link">Connexion</a></strong>
        </div>
    <?php endif; ?>
</div>

<style>
    .counter {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .counter button {
        font-size: 1.2rem;
    }

    .counter .btn-outline-secondary {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 1.5rem;
        transition: background-color 0.3s ease;
    }

    .counter .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }

    .form-control {
        max-width: 70px;
        font-size: 1.1rem;
    }

    .btn-success,
    .btn-danger {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }

    .alert-link {
        font-weight: bold;
    }
</style>