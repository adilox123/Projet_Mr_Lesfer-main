<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$db = "gestion_scolarite";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement ajout matière
if (isset($_POST['add_matiere'])) {
    $nom = $_POST['nom_matiere'];
    $filiere = $_POST['id_filiere'];
    $enseignant = $_POST['id_enseignant'];
    $conn->query("INSERT INTO matieres (nom_matiere, id_filiere, id_enseignant) VALUES ('$nom', $filiere, $enseignant)");
}

// Traitement ajout filière
if (isset($_POST['add_filiere'])) {
    $nom = $_POST['nom_filiere'];
    $conn->query("INSERT INTO filieres (nom_filiere) VALUES ('$nom')");
}

// Traitement ajout évaluation
if (isset($_POST['add_evaluation'])) {
    $etudiant = $_POST['id_etudiant'];
    $matiere = $_POST['id_matiere'];
    $note = $_POST['note'];
    $date = $_POST['date_evaluation'];
    $conn->query("INSERT INTO evaluations (id_etudiant, id_matiere, note, date_evaluation) VALUES ($etudiant, $matiere, $note, '$date')");
}

// Récupération données
$filieres = $conn->query("SELECT * FROM filieres");
$enseignants = $conn->query("SELECT * FROM enseignants");
$etudiants = $conn->query("SELECT * FROM etudiants");
$matieres = $conn->query("SELECT * FROM matieres");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Pédagogique</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #004d40;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 600px;
            margin-bottom: 30px;
        }

        .card-header {
            font-size: 1.2rem;
            font-weight: bold;
            color: #00796b;
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            color: white;
        }

        .btn-primary {
            background-color: #00796b;
        }

        .btn-success {
            background-color: #388e3c;
        }

        .btn-warning {
            background-color: #fbc02d;
            color: #004d40;
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <h1>Gestion Pédagogique</h1>

    <!-- Ajouter une matière -->
    <div class="card">
        <div class="card-header">Ajouter une matière</div>
        <form method="POST">
            <input type="text" name="nom_matiere" placeholder="Nom matière" required>
            <select name="id_filiere" required>
                <option value="">Choisir une filière</option>
                <?php while ($f = $filieres->fetch_assoc()) echo "<option value='{$f['id_filiere']}'>{$f['nom_filiere']}</option>"; ?>
            </select>
            <select name="id_enseignant" required>
                <option value="">Choisir un enseignant</option>
                <?php while ($e = $enseignants->fetch_assoc()) echo "<option value='{$e['id_enseignant']}'>{$e['nom']} {$e['prenom']}</option>"; ?>
            </select>
            <button type="submit" name="add_matiere" class="btn-primary">Ajouter</button>
        </form>
    </div>

    <!-- Ajouter une filière -->
    <div class="card">
        <div class="card-header">Ajouter une filière</div>
        <form method="POST">
            <input type="text" name="nom_filiere" placeholder="Nom filière" required>
            <button type="submit" name="add_filiere" class="btn-success">Ajouter</button>
        </form>
    </div>

    <!-- Ajouter une évaluation -->
    <div class="card">
        <div class="card-header">Ajouter une évaluation</div>
        <form method="POST">
            <select name="id_etudiant" required>
                <option value="">Choisir un étudiant</option>
                <?php while ($et = $etudiants->fetch_assoc()) echo "<option value='{$et['id_etudiant']}'>{$et['nom']} {$et['prenom']}</option>"; ?>
            </select>
            <select name="id_matiere" required>
                <option value="">Choisir une matière</option>
                <?php mysqli_data_seek($matieres, 0); while ($m = $matieres->fetch_assoc()) echo "<option value='{$m['id_matiere']}'>{$m['nom_matiere']}</option>"; ?>
            </select>
            <input type="number" name="note" min="0" max="20" step="0.01" placeholder="Note" required>
            <input type="date" name="date_evaluation" required>
            <button type="submit" name="add_evaluation" class="btn-warning">Ajouter</button>
        </form>
    </div>
</body>
</html>
