<?php
include 'includes/db_config.php';

if (isset($_GET['id'])) {
    $tache_id = $_GET['id'];

    // Récupérer les informations de la tâche à supprimer
    $sql = "SELECT titre, utilisateur_id FROM taches WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tache_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tache = $result->fetch_assoc();

    if ($tache) {
        $titre = $tache['titre'];
        $utilisateur_id = $tache['utilisateur_id'];

        // Supprimer toutes les occurrences de la tâche ayant le même titre et utilisateur
        $sql_delete = "DELETE FROM taches WHERE titre = ? AND utilisateur_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("si", $titre, $utilisateur_id);

        if ($stmt_delete->execute()) {
            header('Location: index.php');
            exit();
        } else {
            echo "Erreur lors de la suppression: " . $stmt_delete->error;
        }
    } else {
        echo "Tâche non trouvée.";
    }
} else {
    echo "ID de la tâche manquant.";
}
?>
