<?php
session_start(); // Démarrer la session

include_once 'include/database.php';

// Récupérer les paramètres depuis l'URL
$idCommande = $_GET['id'];
$etat = $_GET['etat'];

try {
    // Commencer une transaction
    $pdo->beginTransaction();

    // Vérifier la disponibilité des produits
    $quantitesDisponibles = true;
    $messageErreur = '';

    if ($etat == 1) { // Validation de la commande
        // Récupérer les produits de la commande
        $sqlLignesCommande = $pdo->prepare('SELECT ligne_commande.*, produit.quantite as quantite_disponible 
                                            FROM ligne_commande
                                            INNER JOIN produit ON ligne_commande.id_produit = produit.id
                                            WHERE ligne_commande.id_commande = ?');
        $sqlLignesCommande->execute([$idCommande]);
        $lignesCommande = $sqlLignesCommande->fetchAll(PDO::FETCH_OBJ);

        // Vérifier si la quantité demandée est disponible pour chaque produit
        foreach ($lignesCommande as $ligne) {
            if ($ligne->quantite > $ligne->quantite_disponible) {
                $quantitesDisponibles = false;
                $messageErreur .= 'Le produit "' . $ligne->libelle . '" n\'a pas assez de stock disponible. Stock disponible: ' . $ligne->quantite_disponible . '.<br>';
            }
        }

        if ($quantitesDisponibles) {
            // Vérifier et mettre à jour les quantités des produits
            foreach ($lignesCommande as $ligne) {
                $nouvelleQuantite = $ligne->quantite_disponible - $ligne->quantite;
                $sqlUpdateProduit = $pdo->prepare('UPDATE produit SET quantite = ? WHERE id = ?');
                $sqlUpdateProduit->execute([$nouvelleQuantite, $ligne->id_produit]);
            }

            // Mettre à jour l'état de la commande
            $sqlUpdateCommande = $pdo->prepare('UPDATE commande SET valide = ? WHERE id = ?');
            $sqlUpdateCommande->execute([$etat, $idCommande]);

            // Valider la transaction
            $pdo->commit();

            // Ajouter un message de succès à la session
            $_SESSION['success'] = 'La commande a été validée avec succès !';
        } else {
            // Ajouter un message d'erreur à la session
            $_SESSION['error'] = "Erreur : Les produits suivants n'ont pas suffisamment de stock disponible.<br>" . $messageErreur;
        }
    } else { // Annulation de la commande
        // Récupérer les produits de la commande
        $sqlLignesCommande = $pdo->prepare('SELECT ligne_commande.*, produit.quantite as quantite_disponible 
                                            FROM ligne_commande
                                            INNER JOIN produit ON ligne_commande.id_produit = produit.id
                                            WHERE ligne_commande.id_commande = ?');
        $sqlLignesCommande->execute([$idCommande]);
        $lignesCommande = $sqlLignesCommande->fetchAll(PDO::FETCH_OBJ);

        // Réintégrer les produits annulés dans le stock
        foreach ($lignesCommande as $ligne) {
            $nouvelleQuantite = $ligne->quantite_disponible + $ligne->quantite;
            $sqlUpdateProduit = $pdo->prepare('UPDATE produit SET quantite = ? WHERE id = ?');
            $sqlUpdateProduit->execute([$nouvelleQuantite, $ligne->id_produit]);
        }

        // Mettre à jour l'état de la commande
        $sqlUpdateCommande = $pdo->prepare('UPDATE commande SET valide = ? WHERE id = ?');
        $sqlUpdateCommande->execute([$etat, $idCommande]);

        // Valider la transaction
        $pdo->commit();

        // Ajouter un message de succès à la session
        $_SESSION['success'] = 'La commande a été annulée avec succès et les produits ont été réintégrés au stock.';
    }

    // Rediriger vers la page de commandes avec le message
    header('Location: commandes.php');
    exit();

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();

    // Ajouter un message d'erreur à la session
    $_SESSION['error'] = 'Une erreur est survenue : ' . $e->getMessage();

    // Rediriger vers la page de commandes avec le message d'erreur
    header('Location: commandes.php');
    exit();
}
?>
