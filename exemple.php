<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=gestion_scolarite", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$filtre_role = $_GET['role'] ?? 'tous';
$where_clause = ($filtre_role !== 'tous') ? "WHERE role = :role" : "";
$sql = "SELECT * FROM utilisateurs $where_clause";
$stmt = $conn->prepare($sql);

if ($filtre_role !== 'tous') {
    $stmt->bindParam(':role', $filtre_role);
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Tableau de bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        nav {
            background-color: #00796b;
            overflow: hidden;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
        }

        nav li {
            flex: 1;
        }

        nav a {
            display: block;
            text-align: center;
            padding: 14px;
            color: white;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #004d40;
        }

        .container {
            padding: 20px;
            max-width: 1100px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #004d40;
        }

        .filter-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 15px;
            margin: 5px;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary { background-color: #00796b; }
        .btn-primary:hover { background-color: #004d40; }

        .btn-success { background-color: #388e3c; }
        .btn-success:hover { background-color: #2e7d32; }

        .btn-danger { background-color: #d32f2f; }
        .btn-danger:hover { background-color: #c62828; }

        .btn-info { background-color: #0288d1; }
        .btn-info:hover { background-color: #0277bd; }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        .action-cell {
            text-align: center;
        }

        /* Ajout pour la barre secondaire */
        .secondary-nav {
            background-color: #009688;
            padding: 10px;
            text-align: center;
        }
        
        .secondary-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .secondary-nav a:hover {
            background-color: #00796b;
        }
        
        .secondary-nav a.active {
            background-color: #004d40;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>Tableau de bord Admin</h1>
    <p>Bonjour, <?= htmlspecialchars($_SESSION['username']) ?> !</p>
</header>

<nav>
    <ul>
        <li><a href="admin_dashboard.php">Accueil</a></li>
        <li><a href="users.php">Gérer les utilisateurs</a></li>
        <li><a href="Gestion_pedagogique.php">Gestion Pédagogique</a></li>
        <li><a href="admin_stats.php">Statistiques</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<!-- Barre secondaire pour l'emploi des contrôles -->
<div class="secondary-nav">
    <a href="emploi_evaluation.php" class="active">Emploi des Contrôles</a>
</div>

<div class="container">
    <h2>Gestion des utilisateurs</h2>

    <div class="filter-buttons">
        <a href="?role=tous" class="btn btn-primary">Tous</a>
        <a href="?role=admin" class="btn btn-primary">Admins</a>
        <a href="?role=enseignant" class="btn btn-primary">Enseignants</a>
        <a href="?role=etudiant" class="btn btn-primary">Étudiants</a>
        <a href="add_user.php" class="btn btn-info">➕ Ajouter un utilisateur</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Email</th>
                <th class="action-cell">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['gmail']) ?></td>
                    <td class="action-cell">
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-success">Modifier</a>
                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>