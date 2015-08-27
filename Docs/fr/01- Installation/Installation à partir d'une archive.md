Serveur LAMP ou WAMP
====================

Pré-requis: Un serveur LAMP est disponible

Archive Ari'i
=============

'''Demander l'archive à support@sos-paris.com'''

Décompresser l'archive arii.zip

Virtual Host
============

Créer un virtual host qui pointe sur le répertoire web

* Exemple Linux :
 <VirtualHost *:80>
         ServerAdmin webmaster@localhost 
 
         DocumentRoot /home/arii/Symfony/web
         DirectoryIndex app.php
 
         <Directory />
                 Options FollowSymLinks
                 AllowOverride None
         </Directory>
         <Directory /home/arii/Symfony/web/>
                 Options Indexes FollowSymLinks MultiViews
                 AllowOverride All
                 Order allow,deny
                 allow from all
         </Directory> 
 
         ErrorLog ${APACHE_LOG_DIR}/arii_error.log
 
         # Possible values include: debug, info, notice, warn, error, crit,
         # alert, emerg.
         LogLevel warn
 
         CustomLog ${APACHE_LOG_DIR}/arii_access.log combined
         ServerAlias www.arii.org *.arii.org
 </VirtualHost>

* Test URL
Tester l'url http://<serveur>/app_dev.php

= Base de données =

* Configurer la base de données
Editer le fichier app/config/parameters.yml

* Créer la base de données
[root@arii64centos arii]# php app/console doctrine:database:create
Created database for connection named `arii`

* Ajouter les tables
 php app/console doctrine:schema:create
 ATTENTION: This operation should not be executed in a production environment.
 
 Creating database schema...
 Database schema created successfully!

* Créer un nouvel utilisateur
 php app/console arii:user:create
 Please choose a username:operator
 Please choose an email:operator@sos-paris.com
 Please choose a password:operator
 Please choose a firstname:operator
 Please choose a lastname:operator
 Please choose an enterprise:sos-paris
 Created user operator

* Donner un rôle à l'utilisateur
 php app/console fos:user:promote operator ROLE_OPERATOR
 Role "ROLE_OPERATOR" has been added to user "operator".

= Connexion =
* Url http://<serveur>/login
* En tant qu'operator

= Créer un filtre global =
Obsolete.

- Mon compte (en haut à droite)
- Onglet « Filtre »
- Bouton Nouveau
- Donner un titre
- Mettre * sur tous les champs

[[category:Installation Ari'i|Ari'i]][[category:Ari'i]][[category:CentOS]]
