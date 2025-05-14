<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=gestion_scolarite", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $matiere = $_POST['matiere'];
    $classe = $_POST['classe'];
    $type = $_POST['type'];
    
    $stmt = $conn->prepare("UPDATE evaluations SET date_evaluation = ?, matiere = ?, classe = ?, type_controle = ? WHERE id = ?");
    $stmt->execute([$date, $matiere, $classe, $type, $id]);
    
    $_SESSION['message'] = "Évaluation modifiée avec succès!";
    header("Location: emploi_evaluation.php");
    exit();
}               

// Récupération des contrôles
$stmt = $conn->query("SELECT * FROM evaluations ORDER BY date_evaluation ASC");
$controles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérification si l'utilisateur est un enseignant
$is_teacher = ($_SESSION['role'] == 'enseignant');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi des Contrôles</title>
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
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 4px;
        }
        
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
        
        .btn {
            padding: 8px 12px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-warning {
            background-color: #ff9800;
        }
        
        .btn-warning:hover {
            background-color: #f57c00;
        }
        
        .btn-danger {
            background-color: #d32f2f;
        }
        
        .btn-danger:hover {
            background-color: #c62828;
        }
        
        /* Barre de modification en haut */
        .mod-bar {
            background-color: #00796b;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mod-bar input, .mod-bar select {
            padding: 6px;
            margin-left: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .mod-bar button {
            padding: 8px 12px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .mod-bar button:hover {
            background-color: #f57c00;
        }

    </style>
</head>
<body>

<header>
    <h1>Emploi des Contrôles</h1>
</header>

<nav>
    <ul>
        <li><a href="admin_dashboard.php">Accueil</a></li>
        <li><a href="users.php">Gérer les utilisateurs</a></li>
        <li><a href="" class="active">Emploi des Contrôles</a></li>
        <li><a href="Gestion_pedagogique.php">Gestion Pédagogique</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<!-- Barre de modification -->
<?php if ($is_teacher): ?>
    <div class="mod-bar">
        <span>Modifier un contrôle</span>
        <form method="POST" action="emploi_evaluation.php" style="display: flex;">
            <input type="datetime-local" name="date" placeholder="Date et Heure" required>
            <input type="text" name="matiere" placeholder="Matière" required>
            <input type="text" name="classe" placeholder="Classe" required>
            <select name="type" required>
                <option value="DS">Devoir Surveillé</option>
                <option value="TP">Travaux Pratiques</option>
                <option value="Examen">Examen</option>
                <option value="Projet">Projet</option>
            </select>
            <button type="submit" name="modifier">Modifier</button>
        </form>
    </div>
<?php endif; ?>

<div class="container">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <h2>Liste des Contrôles</h2>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Matière</th>
                <th>Classe</th>
                <th>Type</th>
                <?php if ($is_teacher): ?>
                    <th class="action-cell">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($controles as $controle): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($controle['date_evaluation'])) ?></td>
                    <td><?= htmlspecialchars($controle['nom_matiere']) ?></td>
                    <td><?= htmlspecialchars($controle['nom_filiere']) ?></td>
                    <td><?= htmlspecialchars($controle['type_controle']) ?></td>
                    <?php if ($is_teacher): ?>
                        <td class="action-cell">
                            <button onclick="openModal(
                                '<?= $controle['id'] ?>',
                                '<?= date('Y-m-d\TH:i', strtotime($controle['date_evaluation'])) ?>',
                                '<?= htmlspecialchars($controle['matiere']) ?>',
                                '<?= htmlspecialchars($controle['classe']) ?>',
                                '<?= htmlspecialchars($controle['type_controle']) ?>'
                            )" class="btn btn-warning">Modifier</button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Fonction pour ouvrir le modal avec les données du contrôle
    function openModal(id, date, matiere, classe, type) {
        document.getElementById('editId').value = id;
        document.getElementById('date').value = date;
        document.getElementById('matiere').value = matiere;
        document.getElementById('classe').value = classe;
        document.getElementById('type').value = type;
        
        document.getElementById('editModal').style.display = 'block';
    }
</script>

</body>
</html>
