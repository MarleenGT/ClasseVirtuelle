# ClasseVirtuelle

## Description

Ce projet permet de centraliser les cours en distenciel d'un établissement scolaire. Je l'ai développé à l'occasion de mon stage de fin de formation.

## Utilisation

Pour utiliser ce projet, il faut tout d'abord importer la BDD *classevirtuelle.sql* incluse dans le fichier racine du projet. 

Il faut également au moins un compte administrateur. Celui-ci à déjà été créé et les identifiants sont visibles avec la commande `php bin/console secrets:list --reveal`. Si vous utilisez ce projet en production, **changez immédiatement** l'identifiant et mot de passe de ce compte afin d'éviter une faille évidente de sécurité.

La documentation est incluse dans le dossier *documentation*.