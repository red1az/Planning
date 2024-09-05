<?php
// Inclure la configuration de la base de données
include 'includes/db_config.php';

// Récupérer l'ID de l'utilisateur (fixe, par exemple, 1)
$user_id = 1;

// Initialiser la variable $taches et $taches_du_jour
$taches = [];
$taches_du_jour = [];

// Obtenir la date actuelle
$date_aujourd_hui = date('Y-m-d');

// Préparer la requête pour récupérer les tâches de l'utilisateur
$sql = "SELECT t.id, t.titre, t.description, MIN(t.date_debut) AS date_debut, t.heure_debut, MAX(t.date_fin) AS date_fin, t.heure_fin, t.statut 
        FROM taches t 
        WHERE t.utilisateur_id = ? 
        GROUP BY t.titre, t.description, t.heure_debut, t.heure_fin, t.statut
        ORDER BY date_debut ASC, heure_debut ASC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $taches = $result->fetch_all(MYSQLI_ASSOC);

        // Filtrer les tâches pour obtenir uniquement celles du jour
        foreach ($taches as $tache) {
            if ($tache['date_debut'] <= $date_aujourd_hui && $tache['date_fin'] >= $date_aujourd_hui) {
                $taches_du_jour[] = $tache;
            }
        }
    } else {
        echo "<p>Aucune tâche trouvée pour cet utilisateur.</p>";
    }
} else {
    echo "<p>Erreur lors de la préparation de la requête: " . $conn->error . "</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des tâches</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
</head>
<body>
    <h1>Gestionnaire de Tâches</h1>

    <div class="container">
        <div class="left-panel">
            <a href="add_task.php" class="add-task">Ajouter une tâche</a>

            <!-- Tâches du jour -->
            <h2 class="section-header">Tâches du jour</h2>
            <div class="tasks-today">
                <?php if (!empty($taches_du_jour)): ?>
                    <?php foreach ($taches_du_jour as $tache): ?>
                        <div class="task-card">
                            <strong><?php echo htmlspecialchars($tache['titre']); ?></strong>
                            <p><?php echo htmlspecialchars($tache['description']); ?></p>
                            <p><strong>Début:</strong> <?php echo htmlspecialchars($tache['date_debut']); ?> à <?php echo htmlspecialchars($tache['heure_debut']); ?></p>
                            <p><strong>Fin:</strong> <?php echo htmlspecialchars($tache['date_fin']); ?> à <?php echo htmlspecialchars($tache['heure_fin']); ?></p>
                            <p><strong>Statut:</strong> <?php echo htmlspecialchars($tache['statut']); ?></p>
                            <div class="task-actions">
                                <a href="edit_task.php?id=<?php echo $tache['id']; ?>" class="edit">Modifier</a>
                                <a href="delete_task.php?id=<?php echo $tache['id']; ?>" class="delete">Supprimer</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune tâche pour aujourd'hui.</p>
                <?php endif; ?>
            </div>

            <!-- Menu déroulant pour toutes les tâches -->
            <details>
                <summary>Voir toutes les tâches</summary>
                <div class="tasks-container">
                    <?php if (!empty($taches)): ?>
                        <?php foreach ($taches as $tache): ?>
                            <div class="task-card">
                                <strong><?php echo htmlspecialchars($tache['titre']); ?></strong>
                                <p><?php echo htmlspecialchars($tache['description']); ?></p>
                                <p><strong>Début:</strong> <?php echo htmlspecialchars($tache['date_debut']); ?> à <?php echo htmlspecialchars($tache['heure_debut']); ?></p>
                                <p><strong>Fin:</strong> <?php echo htmlspecialchars($tache['date_fin']); ?> à <?php echo htmlspecialchars($tache['heure_fin']); ?></p>
                                <p><strong>Statut:</strong> <?php echo htmlspecialchars($tache['statut']); ?></p>
                                <div class="task-actions">
                                    <a href="edit_task.php?id=<?php echo $tache['id']; ?>" class="edit">Modifier</a>
                                    <a href="delete_task.php?id=<?php echo $tache['id']; ?>" class="delete">Supprimer</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune tâche disponible</p>
                    <?php endif; ?>
                </div>
            </details>
        </div>

        <div class="right-panel">
            <h2 class="section-header">Agenda</h2>
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Modale pour afficher les détails de la tâche -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            <p id="modalDescription"></p>
            <p><strong>Date de début:</strong> <span id="modalStart"></span> à <span id="modalStartTime"></span></p>
            <p><strong>Date de fin:</strong> <span id="modalEnd"></span> à <span id="modalEndTime"></span></p>
            <p><strong>Statut:</strong> <span id="modalStatus"></span></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: '100%',  // S'adapte à la taille du conteneur
                contentHeight: 'auto',  // Ajuste la hauteur du contenu pour éviter le débordement
                slotMinTime: '00:00:00',  // Affiche dès minuit
                slotMaxTime: '24:00:00',  // Affiche jusqu'à minuit
                events: [
                    <?php foreach ($taches as $tache): ?>
                    {
                        title: '<?php echo htmlspecialchars($tache['titre']); ?>',
                        start: '<?php echo $tache['date_debut'] . "T" . $tache['heure_debut']; ?>',
                        end: '<?php echo $tache['date_fin'] . "T" . $tache['heure_fin']; ?>',
                        description: '<?php echo htmlspecialchars($tache['description']); ?>',
                        statut: '<?php echo htmlspecialchars($tache['statut']); ?>',
                    },
                    <?php endforeach; ?>
                ],
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    document.getElementById('modalTitle').innerText = info.event.title;
                    document.getElementById('modalDescription').innerText = info.event.extendedProps.description;
                    document.getElementById('modalStart').innerText = info.event.start.toLocaleDateString();
                    document.getElementById('modalStartTime').innerText = info.event.start.toLocaleTimeString();
                    document.getElementById('modalEnd').innerText = info.event.end ? info.event.end.toLocaleDateString() : 'Non spécifiée';
                    document.getElementById('modalEndTime').innerText = info.event.end ? info.event.end.toLocaleTimeString() : 'Non spécifiée';
                    document.getElementById('modalStatus').innerText = info.event.extendedProps.statut;

                    document.getElementById('taskModal').style.display = 'block';
                }
            });
            calendar.render();

            document.querySelector('.modal .close').addEventListener('click', function() {
                document.getElementById('taskModal').style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == document.getElementById('taskModal')) {
                    document.getElementById('taskModal').style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
