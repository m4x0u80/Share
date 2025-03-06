# Projet de Sauvegarde Automatisée de Base de Données MariaDB

Ce projet permet de réaliser une sauvegarde automatisée d'une base de données MariaDB en incluant les étapes suivantes :
- Export de la base de données
- Chiffrement du fichier de sauvegarde
- Transfert sécurisé vers un serveur distant
- Vérification d'intégrité (optionnel)
- Restauration de la base de données

## Structure des fichiers
- **db_backup.sh** : Crée un dump de la base de données.
- **encrypt_backup.sh** : Chiffre le dump pour assurer la sécurité.
- **transfer_backup.sh** : Transfère le fichier chiffré sur un serveur distant.
- **full_backup.sh** : Exécute l'ensemble du processus de sauvegarde.
- **restore_db.sh** : Restaure la base de données à partir du fichier de sauvegarde chiffré.

## Installation
1. Configurer les paramètres dans chaque script.
2. Utiliser `chmod +x` pour rendre chaque script exécutable.
3. Planifier les sauvegardes en utilisant un cron job.

## Utilisation
- **Sauvegarde** : Lancer `./full_backup.sh` ou utiliser Cron pour l'automatiser.
- **Restauration** : Lancer `./restore_db.sh chemin_du_fichier_chiffré`.
