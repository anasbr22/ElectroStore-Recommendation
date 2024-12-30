<?php
ob_start();
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('location: ../connexion.php');
}




$id = $_POST['id'];
$qty = $_POST['qty'];
$idUtilisateur = $_SESSION['utilisateur']['id'];

if (!isset($_SESSION['panier'][$idUtilisateur])) {
    $_SESSION['panier'][$idUtilisateur] = [];
}

if ($qty == 0) {
    unset($_SESSION['panier'][$idUtilisateur][$id]);
} else {
    $_SESSION['panier'][$idUtilisateur][$id] = $qty;
}

// Requête à l'API Flask pour récupérer les produits recommandés
$product_id = $id;  // ID du produit actuellement sélectionné
$api_url = "http://127.0.0.1:5000/recommend?product_id=" . $product_id;  // URL de l'API Flask
$response = file_get_contents($api_url);  // Appel de l'API

if ($response) {
    $recommended_products = json_decode($response, true);

    // Code pour afficher la modale avec les produits recommandés
    echo "<div id='recommendedModal' style='display:none;'>
            <div style='position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);'>
                <div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background:white; padding: 20px;'>
                    <h3>Produits recommandés :</h3>";

    // Affichage des produits recommandés
    foreach ($recommended_products as $product) {
        echo "<div>";
        echo "<img src='" . $product['image'] . "' alt='" . $product['libelle'] . "' style='width: 100px; height: 100px;'>";
        echo "<p>" . $product['libelle'] . " - " . $product['prix'] . "€</p>";
        echo "</div>";
    }

    echo "<button onclick='closeModal()'>Fermer</button>
        </div>
    </div>
</div>";
}
?>

<script>
    // Fonction pour afficher la boîte de dialogue modale
    function showRecommendedModal() {
        document.getElementById('recommendedModal').style.display = 'block';
    }

    // Fonction pour fermer la boîte de dialogue modale
    function closeModal() {
        document.getElementById('recommendedModal').style.display = 'none';
    }

    // Afficher la modale après que la réponse de l'API soit reçue
    window.onload = function () {
        showRecommendedModal();
    }
</script>

<?php
// Redirection après traitement
header("location:" . $_SERVER['HTTP_REFERER']);


?>