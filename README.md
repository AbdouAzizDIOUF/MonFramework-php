Demarrer le projet:
`ETAPE #1 : INSTALLATION DES PACKAGES`
_-> composer install;_

`ETAPE #2 : CONFIGURER LA BASE DE DONNEES`
_-> MODIFIER LE FICHIER `config/config.php`_

`ETAPE #3 : MIGRATION && ET REMPLISSAGE DE LA BASE`
`-> vendor/bin/phinx migrate // effectue la migration`
`-> vendor/bin/phinx seed:run //permet de REMPLIRE LA BASE`


**DEMARRER L'APPLICATION :**
    `-> php -S localhost:8080 -d display_errors=1 -t public/`