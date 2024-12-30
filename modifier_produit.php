<?php
ob_start();
?>

<!doctype html>
<html lang="en">

<head>
    <?php include 'include/head.php' ?>
    <title>Modifier produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-bar {
            transition: width 0.4s ease;
        }
    </style>
</head>

<body>
    <?php
    require_once 'include/database.php';
    include 'include/nav.php' ?>

    <div class="container py-4">


        <?php
        $id = $_GET['id'];
        $sqlState = $pdo->prepare('SELECT * FROM produit WHERE id=?');
        $sqlState->execute([$id]);
        $produit = $sqlState->fetch(PDO::FETCH_OBJ);

        if (isset($_POST['modifier'])) {
            $libelle = $_POST['libelle'];
            $prix = $_POST['prix'];
            $discount = $_POST['discount'];
            $categorie = $_POST['categorie'];
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];

            $filename = '';
            if (!empty($_FILES['image']['name'])) {
                $image = $_FILES['image']['name'];
                $filename = uniqid() . $image;
                move_uploaded_file($_FILES['image']['tmp_name'], 'upload/produit/' . $filename);
            }

            if (!empty($libelle) && !empty($prix) && !empty($categorie) && !empty($quantite)) {
                if (!empty($filename)) {
                    $query = "UPDATE produit SET libelle=?, prix=?, discount=?, id_categorie=?, description=?, image=?, quantite=? WHERE id=?";
                    $sqlState = $pdo->prepare($query);
                    $updated = $sqlState->execute([$libelle, $prix, $discount, $categorie, $description, $filename, $quantite, $id]);
                } else {
                    $query = "UPDATE produit SET libelle=?, prix=?, discount=?, id_categorie=?, description=?, quantite=? WHERE id=?";
                    $sqlState = $pdo->prepare($query);
                    $updated = $sqlState->execute([$libelle, $prix, $discount, $categorie, $description, $quantite, $id]);
                }

                if ($updated) {
                    echo "<div class='alert alert-success' role='alert'>
                            <i class='bi bi-check-circle'></i> Produit modifié avec succès !
                          </div>";
                    header('location: produits.php');
                } else {
                    echo "<div class='alert alert-danger' role='alert'>
                            <i class='bi bi-x-circle'></i> Une erreur est survenue lors de la modification.
                          </div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>
                        <i class='bi bi-x-circle'></i> Le libellé, le prix, la catégorie et la quantité sont obligatoires.
                      </div>";
            }
        }
        ?>

        <h4 class="mb-4">Modifier produit : id= <?php echo htmlspecialchars(string: $id) ?></h4>

        <div class="card p-4 shadow-sm">
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= $produit->id ?>">

                <div class="mb-3">
                    <label for="libelle" class="form-label">Libelle</label>
                    <input type="text" class="form-control" name="libelle" value="<?= $produit->libelle ?>" required>
                    <div class="invalid-feedback">Le libellé est obligatoire.</div>
                </div>

                <div class="mb-3">
                    <label for="prix" class="form-label">Prix ($)</label>
                    <input type="number" class="form-control" name="prix" step="0.1" min="0"
                        value="<?= $produit->prix ?>" required>
                    <div class="invalid-feedback">Le prix est obligatoire et doit être un nombre positif.</div>
                </div>

                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" class="form-control" name="quantite" min="1" value="<?= $produit->quantite ?>"
                        required>
                    <div class="invalid-feedback">La quantité est obligatoire et doit être un nombre positif.</div>
                </div>


                <div class="mb-3">
                    <label for="discount" class="form-label">Discount</label>
                    <input type="range" class="form-control" name="discount" min="0" max="90"
                        value="<?= $produit->discount ?>" onchange="updateDiscount()">
                    <output id="discountOutput"><?= $produit->discount ?>%</output>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?= $produit->description ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" name="image" onchange="uploadImage()">
                    <div id="progressContainer" class="mt-2" style="display:none;">
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <img src="<?= $produit->image ?>" width="250" class="img-fluid mt-2" alt="Image du produit">
                </div>

                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select name="categorie" class="form-control" required>
                        <option value="">Choisissez une catégorie</option>
                        <?php
                        $categories = $pdo->query('SELECT * FROM categorie')->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $categorie) {
                            $selected = $produit->id_categorie == $categorie['id'] ? 'selected' : '';
                            echo "<option value='" . $categorie['id'] . "' $selected>" . $categorie['libelle'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="modifier" class="btn btn-primary mt-3">Modifier produit</button>
            </form>
        </div>
    </div>

    <script>
        // Mise à jour de l'affichage du discount
        function updateDiscount() {
            let discountValue = document.querySelector('[name="discount"]').value;
            document.getElementById('discountOutput').textContent = discountValue + '%';
        }

        // Upload de l'image avec une barre de progression
        function uploadImage() {
            let inputFile = document.querySelector('[name="image"]');
            let progressContainer = document.getElementById('progressContainer');
            let progressBar = document.getElementById('progressBar');

            progressContainer.style.display = 'block';
            let formData = new FormData();
            formData.append("image", inputFile.files[0]);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "upload_image.php", true);

            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    let percent = (e.loaded / e.total) * 100;
                    progressBar.style.width = percent + '%';
                    progressBar.setAttribute("aria-valuenow", percent);
                }
            });

            xhr.send(formData);
        }

        // Validation Bootstrap 5
        (function () {
            'use strict'
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation')
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
            }, false)
        })()
    </script>
</body>

</html>