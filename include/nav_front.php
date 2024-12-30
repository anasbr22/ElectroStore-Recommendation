<nav class="navbar navbar-expand-lg" style="background-color: #212A3E; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container ">
        <!-- Logo -->
        <a class="navbar-brand fs-4 fw-bold text-light" href="#">
            <i class="fa-solid fa-store"></i> Electrostore
        </a>

        <!-- Bouton mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="d-flex align-items-center w-50">
        <!-- Barre de recherche -->
        <form class="d-flex mx-auto w-100" action="recherche.php" method="GET" style="flex-grow: 1;" >
            <input class="form-control-2 me-2 w-75 rounded" type="search" name="search" placeholder="Rechercher un produit..."
                aria-label="Rechercher"  required >
            <button class="btn btn-outline-light" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
        </div>


        <!-- Logique pour le panier et le backoffice -->
        <?php
        $productCount = 0;
        if (isset($_SESSION['utilisateur'])) {
            $idUtilisateur = $_SESSION['utilisateur']['id'];
            $productCount = count($_SESSION['panier'][$idUtilisateur] ?? []);
        }

        function calculerRemise($prix, $discount)
        {
            return $prix - (($prix * $discount) / 100);
        }
        ?>

        <!-- Boutons actions -->
        <div class="d-flex align-items-center">
            <a class="btn btn-outline-light me-3" href="../front/recommendations.php">
            <i class="fa-regular fa-bell"></i> Recommendation
            </a>
            <a class="btn btn-outline-light me-3" href="../">
                <i class="fa-solid fa-bars-progress"></i>
            </a>
            <a class="btn btn-light text-dark" href="panier.php">
                <i class="fa-solid fa-cart-shopping"></i> Panier
                <span class="badge bg-danger"><?php echo $productCount; ?></span>
            </a>
        </div>
    </div>
</nav>