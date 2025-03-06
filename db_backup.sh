#!/bin/bash
# db_backup.sh

# Configuration de la base de données
DB_NAME="dbshareEquipe"
DB_USER="login8076"
DB_PASS="LQFiqNXHDDzqhCG"

# Création du fichier de sauvegarde
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="backups/${DB_NAME}_backup_${DATE}.sql"

# Exporter la base de données
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_FILE

echo "Sauvegarde de la base de données terminée : $BACKUP_FILE"
echo $BACKUP_FILE  # Renvoie le nom du fichier pour le prochain script
