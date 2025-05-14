<?php
// Sécurisation de la page pour s'assurer que l'utilisateur est un admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #004d40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header a {
            color: #ffffff;
            text-decoration: underline;
            float: right;
            margin-right: 20px;
        }

        nav {
            background-color: #00796b;
            padding: 10px 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        nav li {
            margin: 0 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        main {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            color: #004d40;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        a.btn {
            padding: 6px 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            margin-right: 5px;
        }

        a.btn-edit {
            background-color: #ff9800;
        }

        a.btn-edit:hover {
            background-color: #f57c00;
        }

        a.btn-delete {
            background-color: #d32f2f;
        }

        a.btn-delete:hover {
            background-color: #c62828;
        }

        a.add-link {
            display: inline-block;
            margin-bottom: 15px;
            background-color: #00796b;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        a.add-link:hover {
            background-color: #004d40;
        }

    </style>
</head>
<body>

<header>
    <h1>Bienvenue sur le tableau de bord de l'admin</h1>
    <p>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
    <a href="login.php">Déconnexion</a>
</header>

<nav>
    <ul>
        <li><a href="admin_dashboard.php">Accueil</a></li>
        <li><a href="add_user.php">Gérer les utilisateurs</a></li>
        <li><a href="menu.php">Menu de Gestion</a></li>
        <li><a href="admin_stats.php">Statistiques</a></li>
    </ul>
</nav>

<main>
    <h2>Gestion des utilisateurs</h2>
    <a class="add-link" href="add_user.php">➕ Ajouter un nouvel utilisateur</a>

    <table>
        <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            try {
                $conn = new PDO("mysql:host=localhost;dbname=gestion_scolarite", "root", "");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }

            // Requête pour récupérer tous les utilisateurs
            $stmt = $conn->prepare("SELECT id, username, role FROM utilisateurs");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Affichage des utilisateurs dans le tableau
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>
                        <a class='btn btn-edit' href='edit_user.php?id=" . $user['id'] . "'>Modifier</a>
                        <a class='btn btn-delete' href='delete_user.php?id=" . $user['id'] . "'>Supprimer</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</main>

</body>
</html>
