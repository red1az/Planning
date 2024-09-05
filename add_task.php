<?php
include 'includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $heure_debut = $_POST['heure_debut'];
    $date_fin = isset($_POST['same_day']) ? $date_debut : $_POST['date_fin'];
    $heure_fin = $_POST['heure_fin'];
    $statut = $_POST['statut'];
    $utilisateur_id = 1;  // ID fixe de l'utilisateur

    $current_date = $date_debut;

    while (strtotime($current_date) <= strtotime($date_fin)) {
        $sql = "INSERT INTO taches (titre, description, date_debut, heure_debut, date_fin, heure_fin, statut, utilisateur_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $titre, $description, $current_date, $heure_debut, $current_date, $heure_fin, $statut, $utilisateur_id);

        if (!$stmt->execute()) {
            echo "Erreur: " . $stmt->error;
            break;
        }

        // Passer au jour suivant
        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
    }

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une tâche</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <div class="form-container">
        <h1>Ajouter une nouvelle tâche</h1>
        <form method="post" action="add_task.php">
            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" required>

            <label for="same_day">
                <input type="checkbox" id="same_day" name="same_day" onclick="toggleEndDate()"> La tâche se termine le même jour
            </label>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" required>

            <div class="slider-container">
                <label for="time-range-slider">Sélectionnez la plage horaire:</label>
                <div id="time-range-slider"></div>
                <div class="slider-values">
                    <span id="start-time-display">09:00</span>
                    <span id="end-time-display">17:00</span>
                </div>
            </div>

            <input type="hidden" id="heure_debut" name="heure_debut" value="09:00">
            <input type="hidden" id="heure_fin" name="heure_fin" value="17:00">

            <label for="statut">Statut:</label>
            <select id="statut" name="statut">
                <option value="à faire">À faire</option>
                <option value="en cours">En cours</option>
                <option value="terminé">Terminé</option>
            </select>

            <div class="form-actions">
                <button type="submit">Ajouter la tâche</button>
                <a href="index.php" class="cancel">Annuler</a>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="assets/js/task.js"></script>
</body>
</html>
