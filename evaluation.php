<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_scolarite';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour ajouter une évaluation
function ajouterEvaluation($id_etudiant, $id_matiere, $note, $date_evaluation, $type_controle) {
    global $pdo;
    $sql = "INSERT INTO evaluations (id_etudiant, id_matiere, note, date_evaluation, type_controle) 
            VALUES (:id_etudiant, :id_matiere, :note, :date_evaluation, :type_controle)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_etudiant', $id_etudiant);
    $stmt->bindParam(':id_matiere', $id_matiere);
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':date_evaluation', $date_evaluation);
    $stmt->bindParam(':type_controle', $type_controle);
    return $stmt->execute();
}

// Récupérer toutes les évaluations
function getEvaluations() {
    global $pdo;
    $sql = "SELECT * FROM evaluations";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer une évaluation par ID
function getEvaluationById($id_evaluation) {
    global $pdo;
    $sql = "SELECT * FROM evaluations WHERE id_evaluation = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_evaluation);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mettre à jour une évaluation
function updateEvaluation($id_evaluation, $id_etudiant, $id_matiere, $note, $date_evaluation, $type_controle) {
    global $pdo;
    $sql = "UPDATE evaluations SET 
            id_etudiant = :id_etudiant, 
            id_matiere = :id_matiere, 
            note = :note, 
            date_evaluation = :date_evaluation,
            type_controle = :type_controle
            WHERE id_evaluation = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_evaluation);
    $stmt->bindParam(':id_etudiant', $id_etudiant);
    $stmt->bindParam(':id_matiere', $id_matiere);
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':date_evaluation', $date_evaluation);
    $stmt->bindParam(':type_controle', $type_controle);
    return $stmt->execute();
}

// Supprimer une évaluation
function deleteEvaluation($id_evaluation) {
    global $pdo;
    $sql = "DELETE FROM evaluations WHERE id_evaluation = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_evaluation);
    return $stmt->execute();
}

// Traitements
try {
    if (isset($_POST['ajouter'])) {
        $id_etudiant = $_POST['id_etudiant'];
        $id_matiere = $_POST['id_matiere'];
        $note = $_POST['note'];
        $date_evaluation = $_POST['date_evaluation'];
        $type_evaluation = $_POST['type_controle'];

        if (ajouterEvaluation($id_etudiant, $id_matiere, $note, $date_evaluation, $type_evaluation)) {
            echo "<p class='success'>Évaluation ajoutée avec succès!</p>";
            header("Refresh:0");
        } else {
            echo "<p class='error'>Erreur lors de l'ajout.</p>";
        }
    }

    if (isset($_POST['modifier'])) {
        $id_evaluation = $_POST['id_evaluation'];
        $id_etudiant = $_POST['id_etudiant'];
        $id_matiere = $_POST['id_matiere'];
        $note = $_POST['note'];
        $date_evaluation = $_POST['date_evaluation'];
        $type_evaluation = $_POST['type_controle'];

        if (updateEvaluation($id_evaluation, $id_etudiant, $id_matiere, $note, $date_evaluation, $type_evaluation)) {
            echo "<p class='success'>Évaluation modifiée avec succès!</p>";
            header("Location: ".strtok($_SERVER['REQUEST_URI'], '?'));
        } else {
            echo "<p class='error'>Erreur lors de la modification.</p>";
        }
    }

    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        if (deleteEvaluation($id)) {
            echo "<p class='success'>Évaluation supprimée avec succès!</p>";
            header("Location: ".strtok($_SERVER['REQUEST_URI'], '?'));
        } else {
            echo "<p class='error'>Erreur lors de la suppression.</p>";
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des évaluations</title>
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        h1, h2 { text-align: center; color: #004d40; }
        form, table {
            background: white;
            margin: 0 auto 30px;
            padding: 20px;
            border-radius: 12px;
            max-width: 700px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        label { display: block; margin-bottom: 10px; color: #004d40; font-weight: bold; }
        input[type="number"], input[type="date"], input[type="text"] {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ccc; border-radius: 6px;
        }
        button {
            background: #00796b; color: white;
            padding: 10px 20px; border: none; border-radius: 8px;
            cursor: pointer;
        }
        button:hover { background: #004d40; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; }
        th { background: #00796b; color: white; }
        tr:nth-child(even) { background-color: #f1f1f1; }
        tr:hover { background-color: #e0f7fa; }
        a {
            padding: 6px 12px; background: #00796b; color: white;
            text-decoration: none; border-radius: 6px; font-size: 0.9rem;
        }
        a:hover { background-color: #004d40; }
        .success, .error {
            margin: 10px auto;
            max-width: 600px;
            padding: 10px; border-radius: 6px; text-align: center;
        }
        .success { background: #d0f0e0; color: #004d40; border: 1px solid #a3d9c3; }
        .error { background: #ffdddd; color: #a94442; border: 1px solid #ebccd1; }
    </style>
</head>
<body>
    <h1>Gestion des évaluations</h1>

    <h2>Ajouter une évaluation</h2>
    <form method="post">
        <label>ID Étudiant: <input type="number" name="id_etudiant" required></label>
        <label>ID Matière: <input type="number" name="id_matiere" required></label>
        <label>Note: <input type="number" step="0.1" name="note" required></label>
        <label>Date: <input type="date" name="date_evaluation" required></label>
        <label>Type d'évaluation: <input type="text" name="type_evaluation" required></label>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des évaluations</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>ID Étudiant</th>
            <th>ID Matière</th>
            <th>Note</th>
            <th>Date</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        <?php foreach (getEvaluations() as $eval): ?>
        <tr>
            <td><?= $eval['id_evaluation'] ?></td>
            <td><?= $eval['id_etudiant'] ?></td>
            <td><?= $eval['id_matiere'] ?></td>
            <td><?= $eval['note'] ?></td>
            <td><?= $eval['date_evaluation'] ?></td>
            <td><?= $eval['type_controle'] ?></td>
            <td>
                <a href="?edit=<?= $eval['id_evaluation'] ?>">Éditer</a>
                <a href="?delete=<?= $eval['id_evaluation'] ?>" onclick="return confirm('Supprimer cette évaluation?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php
    // Formulaire de modification
    if (isset($_GET['edit'])) {
        $eval = getEvaluationById($_GET['edit']);
        if ($eval):
    ?>
    <h2>Modifier l'évaluation #<?= $eval['id_evaluation'] ?></h2>
    <form method="post">
        <input type="hidden" name="id_evaluation" value="<?= $eval['id_evaluation'] ?>">
        <label>ID Étudiant: <input type="number" name="id_etudiant" value="<?= $eval['id_etudiant'] ?>" required></label>
        <label>ID Matière: <input type="number" name="id_matiere" value="<?= $eval['id_matiere'] ?>" required></label>
        <label>Note: <input type="number" step="0.1" name="note" value="<?= $eval['note'] ?>" required></label>
        <label>Date: <input type="date" name="date_evaluation" value="<?= $eval['date_evaluation'] ?>" required></label>
        <label>Type d'évaluation: <input type="text" name="type_controle" value="<?= $eval['type_controle'] ?>" required></label>
        <button type="submit" name="modifier">Modifier</button>
    </form>
    <?php endif; } ?>
</body>
</html>
