#!/bin/bash
# full_backup.sh

# Exécution des étapes de sauvegarde
BACKUP_FILE=$(./db_backup.sh)
ENCRYPTED_FILE=$(./encrypt_backup.sh $BACKUP_FILE)
./transfer_backup.sh $ENCRYPTED_FILE
