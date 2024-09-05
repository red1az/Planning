# Gestionnaire de Tâches

Ce projet est un gestionnaire de tâches personnel développé en **PHP** avec une base de données **MySQL**. Il permet de créer, modifier, supprimer et visualiser des tâches. Chaque tâche peut avoir une date et une heure de début et de fin, ainsi qu'un statut. L'application inclut également un calendrier interactif pour visualiser les tâches.

## Fonctionnalités

- **Créer une tâche** : Ajouter une nouvelle tâche avec un titre, une description, une date et heure de début et de fin.
- **Modifier une tâche** : Mettre à jour les détails d'une tâche existante.
- **Supprimer une tâche** : Supprimer une tâche et toutes ses occurrences dans le calendrier.
- **Visualiser les tâches** : Afficher les tâches sous forme de liste et sur un calendrier interactif.
- **Sélection d'heure** : Utilisation de barres de sélection (sliders) pour choisir les heures de début et de fin de chaque tâche.
- **Statut de la tâche** : Gérer les tâches avec différents statuts ("à faire", "en cours", "terminé").

## Technologies utilisées

- **Langage backend** : PHP
- **Base de données** : MySQL
- **Frontend** : HTML, CSS, JavaScript (avec utilisation de `FullCalendar` pour le calendrier)
- **Style et design** : Utilisation de CSS personnalisé pour le design du formulaire et du calendrier
- **Barre de sélection des heures** : Implémentation d'un slider pour sélectionner les heures de début et de fin

## Prérequis

Avant de cloner et d'exécuter ce projet, vous devez installer les logiciels suivants :

- **PHP** (version 7.4 ou supérieure)
- **MySQL** (ou tout autre système de gestion de base de données compatible)
- **Apache ou Nginx** (serveur web)
- **XAMPP** ou **MAMP** (optionnel, pour un environnement local facile à configurer)

## Installation

Suivez ces étapes pour installer et exécuter le projet localement :

1. **Cloner le dépôt GitHub :**
   ```bash
   git clone https://github.com/nom-utilisateur/nom-repo.git
   cd nom-repo
