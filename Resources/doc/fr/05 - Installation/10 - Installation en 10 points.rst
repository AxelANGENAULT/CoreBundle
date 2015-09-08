Installation en 10 points
=========================

Il est possible d'installer Ari'i sur une architecture web existante, la procédure se déroule comme suit :
-	création d'un hôte virtuel Apache
-	décompression de l'archive
-	création de la base de données
-	configuration du portail

Procédures :

1) Un serveur LAMP est disponible

2) Décompresser l'archive arii.zip

3) Créer un virtual host qui pointe sur le répertoire web

4) Configurer la base de données
- Editer le fichier app/config/parameters.yml
- Exemple Linux :
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

5) Tester l'url http://<serveur>/app_dev.php

6) Créer la base de données
    php app/console doctrine:schema:create
    ATTENTION: This operation should not be executed in a production environment.
    Creating database schema...
    Database schema created successfully!

7) Créer un nouvel utilisateur
    php app/console arii:user:create
    Please choose a username:operator
    Please choose an email:operator@sos-paris.com
    Please choose a password:operator
    Please choose a firstname:operator
    Please choose a lastname:operator
    Please choose an enterprise:sos-paris
    Created user operator

8) Donner un rôle à l'utilisateur
    php app/console fos:user:promote operator ROLE_OPERATOR
    Role "ROLE_OPERATOR" has been added to user "operator".

9) Connexion
- Url http://<serveur>/login
- En tant qu'operator

10) Créer un filtre global
- Mon compte (en haut à droite)
- Onglet « Filtre »
- Bouton Nouveau
- Donner un titre
- Mettre * dans tous les champs

