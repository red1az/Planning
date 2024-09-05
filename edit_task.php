<?php
include 'includes/db_config.php';

if (isset($_GET['id'])) {
    $tache_id = $_GET['id'];

    // Récupérer les informations de la tâche
    $sql = "SELECT * FROM taches WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tache_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tache = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $heure_debut = $_POST['heure_debut'];
    $date_fin = isset($_POST['same_day']) ? $date_debut : $_POST['date_fin'];
    $heure_fin = $_POST['heure_fin'];
    $statut = $_POST['statut'];
    $utilisateur_id = 1;  // ID fixe de l'utilisateur

    // Supprimer les occurrences existantes de la tâche
    $sql_delete = "DELETE FROM taches WHERE titre = ? AND utilisateur_id = ? AND date_debut >= ? AND date_fin <= ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("siss", $titre, $utilisateur_id, $date_debut, $date_fin);
    $stmt_delete->execute();

    // Réinsérer les tâches modifiées pour chaque jour entre date_debut et date_fin
    $current_date = $date_debut;
    while (strtotime($current_date) <= strtotime($date_fin)) {
        $sql_insert = "INSERT INTO taches (titre, description, date_debut, heure_debut, date_fin, heure_fin, statut, utilisateur_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssssi", $titre, $description, $current_date, $heure_debut, $current_date, $heure_fin, $statut, $utilisateur_id);

        if (!$stmt_insert->execute()) {
            echo "Erreur: " . $stmt_insert->error;
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
    <title>Modifier une tâche</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <div class="form-container">
        <h1>Modifier la tâche</h1>
        <form method="post" action="edit_task.php?id=<?php echo $tache['id']; ?>">
            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($tache['titre']); ?>" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($tache['description']); ?></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($tache['date_debut']); ?>" required>

            <label for="same_day">
                <input type="checkbox" id="same_day" name="same_day" onclick="toggleEndDate()"> La tâche se termine le même jour
            </label>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($tache['date_fin']); ?>" required>

            <div class="slider-container">
                <label for="time-range-slider">Sélectionnez la plage horaire:</label>
                <div id="time-range-slider"></div>
                <div class="slider-values">
                    <span id="start-time-display">09:00</span>
                    <span id="end-time-display">17:00</span>
                </div>
            </div>

            <input type="hidden" id="heure_debut" name="heure_debut" value="<?php echo htmlspecialchars($tache['heure_debut']); ?>">
            <input type="hidden" id="heure_fin" name="heure_fin" value="<?php echo htmlspecialchars($tache['heure_fin']); ?>">

            <label for="statut">Statut:</label>
            <select id="statut" name="statut">
                <option value="à faire" <?php if ($tache['statut'] == 'à faire') echo 'selected'; ?>>À faire</option>
                <option value="en cours" <?php if ($tache['statut'] == 'en cours') echo 'selected'; ?>>En cours</option>
                <option value="terminé" <?php if ($tache['statut'] == 'terminé') echo 'selected'; ?>>Terminé</option>
            </select>

            <div class="form-actions">
                <button type="submit">Mettre à jour la tâche</button>
                <a href="index.php" class="cancel">Annuler</a>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="assets/js/task.js"></script>
</body>
</html>
