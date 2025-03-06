#!/bin/bash
# restore_db.sh

# Fichier de sauvegarde chiffré en entrée
ENCRYPTED_FILE=$1
DECRYPTED_FILE="${ENCRYPTED_FILE%.enc}.sql"
PASSPHRASE="phrase_secrete"

# Déchiffrement
openssl enc -d -aes-256-cbc -in $ENCRYPTED_FILE -out $DECRYPTED_FILE -k $PASSPHRASE

# Configuration de la base de données
DB_NAME="dbshareEquipe"
DB_USER="login80076"
DB_PASS="LQFiqNXHDDzqhCG"

# Restauration de la base de données
mysql -u $DB_USER -p$DB_PASS $DB_NAME < $DECRYPTED_FILE

echo "Base de données restaurée à partir de : $DECRYPTED_FILE"
