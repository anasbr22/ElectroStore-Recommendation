
<?php
ob_start();
?>

<!doctype html>
<html lang="en">

<head>
    <?php include 'include/head.php' ?>
    <title>Ajouter produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" rel="stylesheet">
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
        <h4 class="mb-4">Ajouter un produit</h4>

        <?php
        if (isset($_POST['ajouter'])) {
            $libelle = $_POST['libelle'];
            $prix = $_POST['prix'];
            $discount = $_POST['discount'];
            $categorie = $_POST['categorie'];
            $description = $_POST['description'];
            $date = date('Y-m-d');
            $quantite = $_POST['quantite'];

            $filename = 'produit.png';
            if (!empty($_FILES['image']['name'])) {
                $image = $_FILES['image']['name'];
                $filename = uniqid() . $image;
                move_uploaded_file($_FILES['image']['tmp_name'], 'upload/produit/' . $filename);
            }

            if (!empty($libelle) && !empty($prix) && !empty($categorie) && !empty($quantite)) {
                $sqlState = $pdo->prepare('INSERT INTO produit (libelle, prix, discount, id_categorie, date_ajout, description, image, quantite) VALUES (?,?,?,?,?,?,?,?)');
                $inserted = $sqlState->execute([$libelle, $prix, $discount, $categorie, $date, $description, $filename, $quantite]);
                if ($inserted) {
                    echo "<div class='alert alert-success' role='alert'>Produit ajouté avec succès!</div>";
                    header('location: produits.php');
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Erreur lors de l'ajout du produit.</div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>Le libellé, prix, catégorie et quantité sont obligatoires.</div>";
            }
        }
        ?>

        <div class="card p-3 shadow-sm">
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libelle</label>
                    <input type="text" class="form-control" id="libelle" name="libelle" required>
                    <div class="invalid-feedback">
                        Veuillez entrer un libellé pour le produit.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="prix" class="form-label">Prix (€)</label>
                    <input type="number" class="form-control" id="prix" name="prix" step="0.1" min="0" required>
                    <div class="invalid-feedback">
                        Veuillez entrer un prix valide.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
                    <div class="invalid-feedback">
                        Veuillez entrer une quantité valide.
                    </div>
                </div>


                <div class="mb-3">
                    <label for="discount" class="form-label">Discount</label>
                    <input type="range" value="0" class="form-control" id="discount" name="discount" min="0" max="90">
                    <output id="discountOutput">0%</output>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image" name="image" onchange="uploadImage()">
                    <div id="progressContainer" class="mt-2" style="display:none;">
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <?php
                $categories = $pdo->query('SELECT * FROM categorie')->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="">Choisissez une catégorie</option>
                        <?php
                        foreach ($categories as $categorie) {
                            echo "<option value='" . $categorie['id'] . "'>" . $categorie['libelle'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Veuillez choisir une catégorie.
                    </div>
                </div>

                <button type="submit" name="ajouter" class="btn btn-primary my-2">Ajouter produit</button>
            </form>
        </div>
    </div>

    <!-- Validation du formulaire Bootstrap 5 -->
    <script>
        // Affiche la valeur de discount
        document.getElementById('discount').addEventListener('input', function () {
            document.getElementById('discountOutput').value = this.value + '%';
        });

        // Upload image avec barre de progression
        function uploadImage() {
            let inputFile = document.getElementById('image');
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