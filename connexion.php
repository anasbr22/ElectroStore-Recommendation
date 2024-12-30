<?php
session_start(); // Toujours commencer la session avant tout code HTML ou PHP

if (isset($_POST['connexion'])) {
    $login = $_POST['login'];
    $pwd = $_POST['password'];
    $_SESSION['utilisateur'] = $login;
    $_SESSION['mot_de_passe'] = $pwd;

    if (!empty($login) && !empty($pwd)) {
        require_once 'include/database.php';
        $sqlState = $pdo->prepare('SELECT * FROM utilisateur WHERE login=? AND password=?');
        $sqlState->execute([$login, $pwd]);
        if ($sqlState->rowCount() >= 1 ) {
            $_SESSION['utilisateur'] = $sqlState->fetch();
            header('Location: admin.php');
            exit; // Toujours mettre exit après header() pour arrêter l'exécution du script
        } else {
            echo '<div class="alert alert-danger">Login ou mot de passe incorrect.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Login et mot de passe sont obligatoires.</div>';
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <?php include 'include/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Ajout du CSS moderne -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://img.freepik.com/free-vector/white-abstract-background_23-2148809724.jpg?t=st=1731705824~exp=1731709424~hmac=01eea458eb6633dabb1c15395cd132eca67d821716a792a86dda728f83625b42&w=1060'); 
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.85);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a6268;
            border-color: #4e555b;
        }
        .form-label {
            color: #333;
        }
        .text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <?php include 'include/nav.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 15px;">
            <h4 class="text-center mb-4" style="color: #333;">Connexion</h4>
            <form method="post">
                <div class="mb-3">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" class="form-control" id="login" name="login" required aria-describedby="loginHelp">
                    <small id="loginHelp" class="form-text text-muted">Entrez votre identifiant</small>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required aria-describedby="passwordHelp">
                    <small id="passwordHelp" class="form-text text-muted">Entrez votre mot de passe</small>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2" name="connexion">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="forgot-password.php" class="text-muted">Mot de passe oublié ?</a>
            </div>
        </div>
    </div>

    <!-- Ajout de Bootstrap JS et dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
