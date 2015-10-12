Intégration de moteurs externes
===============================
Si Open Source JobScheduler est déjà déployé sur le site, on peut choisir de rapatrier ses traitements dans le hot folder du job scheduler de la machine virtuelle ou conserver le JobScheduler existant et le connecter à la machine virtuelle pour déployer de nouveaux traitements et sécuriser les moteurs distants.

Arii est un superviseur livré avec la machine virtuelle, si un supervisor est déjà installé il est préférable de ne conserver que celui qui est sur Ari'i car ses objets sont directements accessibles à travers l'interface. Sinon, il faudra partager le répertoire config du superviseur distant.

Connexion sur le supervisor
---------------------------

Pour connecter le moteur sur un superviseur, il faut ajouter modifier le ficher scheduler.xml et ajouter le nom ou l'adresse IP du superviseur ainsi que son port.
Dans notre, le superviseur écoute sur l'__IP 192.168.61.130__ et sur le port __44444__ :

    <spooler>
        <config mail_xslt_stylesheet = "config/scheduler_mail.xsl"
            port = "1801" supervisor="192.168.61.130:44444">


Après l'arrêt/relance du JobScheduler, on peut se connecter sur le superviseur (http://192.168.61.130:44444) pour voir apparaître la machine dans l'onglet __« Remote Schedulers »__ du superviseur.

## Déploiement d'objet

Dans notre exemple, le moteur distant apparaît avec l'adresse 192.168.61.1 et le port indiqué dans le fichier scheduler.xml est 1801. Pour déployer des objets à partir du superviseur, il faut simplement créer un répertoire dédié dans config/remote.

On se connecte sur le serveur avec le compte arii et on vérifie que le moteur distant est accessible en utilisant, par exemple, une commande wget :

    arii@arii64:/tmp$ wget http://192.168.61.1:1801
    --2015-03-17 15:53:42--  http://192.168.61.1:1801/
    Connexion vers 192.168.61.1:1801...connecté.
    requête HTTP transmise, en attente de la réponse...200 OK
    Longueur: non spécifié [text/html]
    Sauvegarde en : «index.html»
    
        [ <=>                                   ] 7 764       --.-K/s   ds 0s
    
    2015-03-17 15:53:42 (367 MB/s) - «index.html» sauvegardé [7764]

Si le superviseur ne peut pas se connecter, on devrait avoir un message de ce type :

    arii@arii64:/tmp$ wget http://192.168.61.1:1802
    --2015-03-17 15:55:03--  http://192.168.61.1:1802/
    Connexion vers 192.168.61.1:1802...échec: Connexion refusée.

Dans ce cas, il faut vérifier :
- que l'adresse IP et le port sont corrects
- que le moteur distant n'est pas protégé par un parefeu
- qu'il autorise bien les connexions externes

Pour ce dernier point, l'adresse IP du superviseur peut être ajoutée dans le fichier scheduler.xml de la manière suivante :
     	<security ignore_unknown_hosts = "yes">
     		<allowed_host host = "192.168.61.130" level = "all"/>
     ...
     	</security>

Pour ajouter un répertoire de déploiement sur cette machine en particulier, on va créer un répertoire avec les paramètres de connexions, adresse IP et port, séparés par un dièze (#) :

    arii@arii64:~/jobscheduler/arii/config/remote$ mkdir 192.168.61.1#1801

Pour vérifier le bon fonctionnement, on créé un nouveau répertoire qui apparaîtra sur l'interface web du moteur distant :

    arii@arii64:~/jobscheduler/arii/config/remote/192.168.61.1#1801$ mkdir « FOR 18RC1 »

Si on se connecte sur le moteur distant, on peut voir qu'un répertoire « FOR 18RC1 » a été créé dans config/cache

On peut aussi vérifier le déploiement à travers _all qui permet de déployer sur l'ensemble des abonnés de ce supervisor :

    arii@arii64:~/jobscheduler/arii/config/remote$ cd _all
    arii@arii64:~/jobscheduler/arii/config/remote/_all$ mkdir "FOR ALL"

Redirection
-----------
Pour simplifier les accès aux moteurs distants, on utilise la fonctionnalité proxy du serveur Apache pour offrir à l'utilisateur une url de type http://<Arii>/js/<scheduler>/ qui évite d'avoir à connaître les adresses et le port de chaque moteur.

Pour ajouter une nouvelle adresse, on doit se connecter avec le compte root sur la machine virtuelle et aller dans la configuration apache :

    arii@arii64:/tmp$ cd /etc/apache2/sites-enabled/

puis éditer le fichier arii dans lequel on ajoutera les 2 lignes suivantes 	:
    	ProxyPass /js/18RC1/  http://192.168.0.246:1801
    	ProxyPassReverse /js/18RC1/  http://192.168.0.246:1801/

On sauvegarde le fichier et on redémarre le serveur web :
    root@arii64:/etc/apache2/sites-enabled# service apache2 restart

On peut ensuite vérifier l'url simplifiée : http://192.168.61.130/js/18RC1/

Si le moteur est une grille de calcul dans une configuration d'équilibrage de charges, on peut déclarer les moteursde la manière suivante :
    <Proxy balancer://grid/ >
    BalancerMember http://127.0.0.1:5555/
    BalancerMember http://127.0.0.1:5556/
    BalancerMember http://127.0.0.1:5557/
    </Proxy>

Puis indiquer le nom du groupe de machines :
        ProxyPass /js/grid/  balancer://grid/
        ProxyPassReverse /js/grid/ balancer://grid/

Si le moteur est en mode haute disponibilité, on indique les machines du groupe en précisant status=+H pour la machine qui doit être utilisé si et seulement si le serveur primaire ne répond plus :

    <Proxy balancer://scheduler/ >
    BalancerMember http://127.0.0.1:4444
    BalancerMember http://127.0.0.1:4445 status=+H
    </Proxy>

L'utilisation du « balancer » se fait de la même manière que pour la grillle de calcul :
        ProxyPass /js/scheduler/  balancer://scheduler/
        ProxyPassReverse /js/scheduler/ balancer://scheduler/


Contenu du fichier __/etc/apache2/sites-enabled/arii__:

    	<Proxy balancer://grid/ >
    	BalancerMember http://127.0.0.1:5555/
    	BalancerMember http://127.0.0.1:5556/
        	BalancerMember http://127.0.0.1:5557/
    	</Proxy>
    	
    	<Proxy balancer://scheduler/ >
    	BalancerMember http://127.0.0.1:4444
    	BalancerMember http://127.0.0.1:4445 status=+H
    	</Proxy>
    	
    	
    	<VirtualHost *:80>
    	        ServerName arii64
    	        ServerAdmin webmaster@localhost
    	
    	    ProxyPass /js/18RC1/  http://192.168.0.246:1801/
    	    ProxyPassReverse /js/18RC1/  http://192.168.0.246:1801/
    	
    	    ProxyPass /js/arii/  http://127.0.0.1:44444/
    	    ProxyPassReverse /js/arii/  http://127.0.0.1:44444/
    	
    	    ProxyPass /js/scheduler/  balancer://scheduler/
    	    ProxyPassReverse /js/scheduler/ balancer://scheduler/
    	
    	    ProxyPass /js/backup/  http://127.0.0.1:4445/
    	    ProxyPassReverse /js/backup/  http://127.0.0.1:4445/

