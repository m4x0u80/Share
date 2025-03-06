#!/bin/bash
# transfer_backup.sh

# Fichier chiffré en entrée
ENCRYPTED_FILE=$1
REMOTE_USER="utilisateur_distant"
REMOTE_HOST="adresse_ip_ou_nom_domaine"
REMOTE_PATH="/var/www/html/equipe/Share/backups"

# Transfert sécurisé
scp $ENCRYPTED_FILE $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH

echo "Fichier transféré sur le serveur distant : $REMOTE_HOST"
