<?php
// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=localhost;dbname=gestion_scolarite", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail = $_POST['gmail'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE gmail = ?");
    $stmt->execute([$gmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'enseignant') {
            header("Location: enseignant_dashboard.php");
        } elseif ($user['role'] === 'etudiant') {
            header("Location: etudiant_dashboard.php");
        } else {
            header("Location: login.php");
        }
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            color: #004d40;
            margin-bottom: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 320px;
        }

        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            color: #00796b;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #00796b;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        input[type="submit"] {
            background-color: #00796b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #004d40;
        }

        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Connexion</h2>
    <form method="post">
        <label for="gmail">Gmail :</label>  
        <input type="email" name="gmail" id="gmail" placeholder="Adresse Gmail" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" placeholder="Mot de passe" required>

        <input type="submit" value="Se connecter">

        <?php if (!empty($erreur)) echo "<div class='error'>$erreur</div>"; ?>
    </form>

</body>
</html>
