<?php
session_start();
require_once '../include/database.php';
?>
<!doctype html>
<html lang="en">

<head>
    <?php include '../include/head_front.php' ?>
    <title>Mon Panier</title>
    <!-- Ajout de styles personnalisés -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style> form.d-flex .form-control {
            border-radius: 20px;
            padding: 10px 15px;
        }

        form.d-flex .btn {
            border-radius: 20px;
        }</style>
</head>

<body>
    <?php include '../include/nav_front.php' ?>
    <div class="container py-5">
        <?php
        if (isset($_POST['vider'])) {
            $_SESSION['panier'][$idUtilisateur] = [];
            header('location: panier.php');
        }

        $idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
        $panier = $_SESSION['panier'][$idUtilisateur] ?? [];

        if (!empty($panier)) {
            $idProduits = array_keys($panier);
            $idProduits = implode(',', $idProduits);
            $produits = $pdo->query("SELECT * FROM produit WHERE id IN ($idProduits)")->fetchAll(PDO::FETCH_ASSOC);
        }

        if (isset($_POST['valider'])) {
            $sql = 'INSERT INTO ligne_commande(id_produit, id_commande, prix, quantite, total) VALUES';
            $total = 0;
            $prixProduits = [];
            foreach ($produits as $produit) {
                $idProduit = $produit['id'];
                $qty = $panier[$idProduit];
                $discount = $produit['discount'];
               // $prix = calculerRemise($produit['prix'], $discount);
               $prix =$produit['discount'];
               
                $total += $qty * $prix;
                $prixProduits[$idProduit] = [
                    'id' => $idProduit,
                    'prix' => $prix,
                    'total' => $qty * $prix,
                    'qty' => $qty
                ];
            }

            $sqlStateCommande = $pdo->prepare('INSERT INTO commande(id_client,total) VALUES(?,?)');
            $sqlStateCommande->execute([$idUtilisateur, $total]);
            $idCommande = $pdo->lastInsertId();
            $args = [];
            foreach ($prixProduits as $produit) {
                $id = $produit['id'];
                $sql .= "(:id$id,'$idCommande',:prix$id,:qty$id,:total$id),";
            }
            $sql = substr($sql, 0, -1);
            $sqlState = $pdo->prepare($sql);
            foreach ($prixProduits as $produit) {
                $id = $produit['id'];
                $sqlState->bindParam(':id' . $id, $produit['id']);
                $sqlState->bindParam(':prix' . $id, $produit['prix']);
                $sqlState->bindParam(':qty' . $id, $produit['qty']);
                $sqlState->bindParam(':total' . $id, $produit['total']);
            }
            $inserted = $sqlState->execute();
            if ($inserted) {
                $_SESSION['panier'][$idUtilisateur] = [];
                header('location: panier.php?success=true&total=' . $total);
            } else {
                ?>
                <div class="alert alert-danger" role="alert">
                    Une erreur est survenue, veuillez réessayer plus tard.
                </div>
                <?php
            }
        }

        if (isset($_GET['success'])) {
            ?>
            <h1>Merci !</h1>
            <div class="alert alert-success" role="alert">
                Votre commande d'un montant total de <strong><?php echo $_GET['total'] ?? 0 ?> $</strong> a été validée.
            </div>
            <hr>
            <?php
        }

        if (!isset($_GET['success'])) {
            ?>
            <h4>Panier (<?php echo count($panier); ?> produit(s))</h4>
            <?php
        }
        ?>
        <div class="row">
            <?php
            if (empty($panier)) {
                if (!isset($_GET['success'])) {
                    ?>
                    <div class="alert alert-warning col-12" role="alert">
                        Votre panier est vide ! Commencez vos achats <a href="./index.php" class="btn btn-success btn-sm">Acheter des produits</a>
                    </div>
                    <?php
                }
            } else {
                ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Libellé</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Remise</th>
                            <th>Prix après Remise</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($produits as $produit) {
                            $idProduit = $produit['id'];
                            $totalProduit = $produit['discount']* $panier[$idProduit];
                            $total += $totalProduit;
                            $remisePourcentage = (int)(100-(($produit['discount']*100) / $produit['prix']));
                            ?>
                            <tr>
                                <td><?php echo $produit['id']; ?></td>
                                <td><img src="<?php echo $produit['image']; ?>" alt="<?php echo $produit['libelle']; ?>" width="80px"></td>
                                <td><?php echo $produit['libelle']; ?></td>
                                <td><?php include '../include/front/counter.php' ?></td>
                                <td><strike><?php echo $produit['prix']; ?> $</strike></td>
                                <td>- <?= $remisePourcentage; ?>%</td>
                                <td><?php echo  $produit['discount']; ?> $</td>
                                <td><?php echo $totalProduit; ?> $</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" align="right"><strong>Total</strong></td>
                            <td><?php echo $total; ?> $</td>
                        </tr>
                        <tr>
                            <td colspan="8" align="right">
                                <form method="post">
                                    <input type="submit" class="btn btn-success" name="valider" value="Valider la commande">
                                    <input type="submit" class="btn btn-danger" name="vider" value="Vider le panier" onclick="return confirm('Voulez-vous vraiment vider le panier ?')">
                                </form>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
</body>

</html>
