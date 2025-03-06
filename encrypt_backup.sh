#!/bin/bash
# encrypt_backup.sh

# Fichier de sauvegarde en entrée
BACKUP_FILE=$1
ENCRYPTED_FILE="${BACKUP_FILE}.enc"
PASSPHRASE="phrase_secrete"

# Chiffrement
openssl enc -aes-256-cbc -salt -in $BACKUP_FILE -out $ENCRYPTED_FILE -k $PASSPHRASE

# Suppression de la sauvegarde non chiffrée
rm $BACKUP_FILE

echo "Fichier de sauvegarde chiffré : $ENCRYPTED_FILE"
echo $ENCRYPTED_FILE  # Renvoie le fichier chiffré pour le prochain script
