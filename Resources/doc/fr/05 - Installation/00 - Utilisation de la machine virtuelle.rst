Machine virtuelle
=================

L'utilisation de la machine virtuelle est le moyen le plus simple de démarrer avec l'automate d'exploitation et ses différents composants.  Elle est pré-configurée permet une intégration rapide puisqu'elle doit simplement être téléchargée et ouverte avec VM player ou intégrée dans un ESX. La finalité est un ensemble cohérent de composants interagissant entre eux.

Contenu
-------
La machine repose sur un serveur LAMP qui représente l'infrastructure la plus utilisée dans un environnement web, ce standard permet de bénéficier de compétences sur site. Le serveur Linux est une Debian 7 64bits, un serveur Apache2, une base de données MariaDB et PHP 5.4. Si vous utilisez d'autres standards vous pouvez vous reporter au chapitre Compatibilités.

### Composants

La machine virtuelle contient différents logiciels composants la solution, on peut distinguer 2 couches principales qu'il est possible de dissocier:
- l'infrastructure d'ordonnancement basé sur Open Source JobScheduler
- le portail web Symfony2 (Apache/MariaDB/PHP)

### Logiciels

Les logiciels utilisés sont en licence open source, librement distribuables dans le même mode de licence. 

| Composant	| Description   | 
| ------------- | ------------- | 
| MariaDB	| Base de données    | 
| Nginx         | Serveur web   | 
| PHP           | Langage de script pour la partie Web   | 
| Perl          | Langage de script pour la partie système   | 
| Symfony2	| Framework PHP   | 
| DHTMLx	| Framework Javascript   | 
| Java 1.7	| Environnement Java   | 
| Open Source JobScheduler 1.8	| Ordonnanceur open source   | 
| Graphviz	| Outil de création graphique   | 
| Git           | Gestionnaire de versions   | 
| NTP           | Serveur de temps   | 

Intégration dans le S.I.
------------------------

L'intégration dépend du système d'information, on peut identifier trois niveaux d'utilisation en fonction du volume et des technologies présentes dans le parc informatique :
- pour les plus petits parc, la machine peut être exécutée dans un « VM Player », cet outil gratuit peut être installé sur différents systèmes d'exploitation
- pour les parcs disposant d'un ESX, la machine peut être directement intégrée et profiter des bénéfices de cette architecture
- pour les parcs avec des normes strictes sur les composants et les méthodes d'installation, il faut vérifier la compatibilité et installer manuellement  

### VM Player

Vous pouvez tester la machine directement sur votre station de travail en utilisant VM Player que vous pourrez télécharger gratuitement à cette adresse : https://my.vmware.com/web/vmware/free#desktop_end_user_computing/vmware_player/7_0

### ESX/ESXi

Pour une utilisation en entreprise, il est préférable d'importer la machine virtuelle dans un ESX ou un ESXi.  Le format étant de type « hosted » (-sxx), il sera nécessaire de convertir la machine virtuelle en un format utilisable sur l'ESX.  

__En ligne de commande :__

Chargement du module multiextent:
    vmkload_mod multiextent
Conversion:
    vmkfstools -i ./Arii64.vmdk Arii64_esx.vmdk -d thin
Suppression de l'ancien disque:
    vmkfstools -U Arii64.vmdk
Renommage du nouveau avec l'ancien nom:
    vmkfstools -E Arii64_esx.vmdk Arii64.vmdk
Déchargement du module multiextent:
    vmkload_mod -u multiextent

On ne doit plus avoir qu'un petit fichier Arii64.vmdk et un gros (le disque) Arii64-flat.vmdk.

__Par le VMware vCenter Converter Standalone :__
Ce logiciel qui peut être téléchargé gratuitement  à cette adresse http://www.vmware.com/products/converter
permet de convertir une machine virtuelle pour l'importer sur l'ESX.
 
Administration
--------------
Ce chapitre fournit les procédures basiques pour l'administration de la machine virtuelle.

### Connexion

Pour se connecter sur la machine, on dispose de différents comptes :
| Login     | Mot de passe	| Description
| root      | root          | Compte disposant de tous les droits
| osjs      | osjs          | Compte utilisé pour la gestion de l'ordonnanceur
| www-data  |               | Compte pour la partie web

Il est préférable de changer les mots de passe, ce changement n'a pas d'impact sur la machine virtuelle.

### Adresse IP

Si vous utilisez VM Player, la première chose est de connaître l'adresse IP utilisée pour la machine. Vous devez être connecté en root et taper la commande ifconfig.
    root@arii:~# ifconfig
    eth0      Link encap:Ethernet  HWaddr 00:0c:29:bb:1b:88
              inet adr:192.168.61.140  Bcast:192.168.61.255  Masque:255.255.255.0
              adr inet6: fe80::20c:29ff:febb:1b88/64 Scope:Lien
              UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
              RX packets:340529 errors:1 dropped:0 overruns:0 frame:0
              TX packets:348105 errors:0 dropped:0 overruns:0 carrier:0
              collisions:0 lg file transmission:1000
              RX bytes:244483659 (233.1 MiB)  TX bytes:348854401 (332.6 MiB)
              Interruption:19 Adresse de base:0x2000
    
    lo        Link encap:Boucle locale
              inet adr:127.0.0.1  Masque:255.0.0.0
              adr inet6: ::1/128 Scope:Hôte
              UP LOOPBACK RUNNING  MTU:16436  Metric:1
              RX packets:133296 errors:0 dropped:0 overruns:0 frame:0
              TX packets:133296 errors:0 dropped:0 overruns:0 carrier:0
              collisions:0 lg file transmission:0
              RX bytes:26737903 (25.4 MiB)  TX bytes:26737903 (25.4 MiB)

Cette adresse vous permet de vous connecter directement sur l'interface web avec l'adresse IP obtenue. 
Dans notre exemple : http://192.168.61.14
 
Open Source JobScheduler
------------------------

Le compte osjs doit être utilisé pour toutes les actions sur l'ordonnanceur.
JobScheduler est installé dans le répertoire /opt/jobscheduler/scheduler
Les fichiers utilisateurs sont dans /home/osjs/jobscheduler
Pour vérifier le bon fonctionnement de l'ordonnanceur, il faut se connecter sur l'url du serveur web embarqué : http://<adresse IP>:4444

### Arrêt/relance
Pour Démarrer l'ordonnanceur :
- taper /etc/init.d/jobscheduler start
- indiquer le mot de passe root
    osjs@arii:~$ /etc/init.d/jobscheduler start
    Starting JobScheduler...
    Mot de passe :

Pour l'arrêter : 
    /etc/init.d/jobscheduler stop
    osjs@arii:~$ /etc/init.d/jobscheduler stop
    
    ________________________________________________________________________
    
    Job Scheduler instance: scheduler
    .............. version: 1.6-SNAPSHOT
    ......... operated for: arii:4444
    ........ running since: 2014-02-27T03:02:27Z
    ................ state: running
    ............. cpu used:
    ........... job chains: 6
    ................. jobs: 20
    ............... orders: 3
    ..... remote instances: 0, connected instances:
    ________________________________________________________________________
    
    No jobs are running for this instance
    Shutting down JobScheduler...
    <?xml version="1.0" encoding="ISO-8859-1"?><spooler><answer time="2014-02-28T02:04:57.465Z"><ok/></answer></spooler>

Symfony
-------

Symfony est installé dans le répertoire __/usr/share/symfony2__
Le compte utilisé pour cette partie est __www-data__
    root@arii:~# su - www-data
    Pas de répertoire, connexion avec HOME=/

### Configuration

L'interface est configurée pour dialoguer avec l'ordonnanceur de la machine virtuelle. Il est possible de modifier ces paramètres pour connecter l'interface sur une base de données et un JobScheduler existant afin de contrôler un installation existante.
Le fichier de paramètres est __/usr/share/symfony2/app/config/parameters.yml__
| Paramètre         | Description               | Valeur par défaut
| database_driver   | Type de base de données   | 	pdo_mysql
| database_host     | Hôte de la base           | localhost
| database_port     | Port de connexion         | 3306
| database_name     | Nom de la base            | arii
| database_user     | Utilisateur de connexion	| root
| database_password | Mot de passe              | null
| database_path     | Répertoire                | null
| mailer_transport  | Type de messagerie	| smtp
| mailer_host       | Hôte de message           | 127.0.0.1
| mailer_user       | Utilisateur  de connexion | null
| mailer_password   | Mot de passe              | null
| locale            | Langue par défaut         | fr

Pour une configuration plus complexe permettant de gérer de multiples sites, bases de données ou moteurs, il faut utiliser le module d'administration qui permet de définir son environnement technique.

Utilisateurs
------------
La gestion des utilisateurs utilise les mécanismes Symfony et son mode console, il est suffisant pour ajouter rapidement quelques utilisateurs. Pour le traitement d'un plus grand volume de comptes, il est préférable d'utiliser le module d'administration.

Toutes les commandes sont exécutées avec le compte www-data. Pour utiliser la console, il faut se déplacer dans le répertoire de Symfony puis préfixer les commandes par php app/console
    $ cd /usr/share/symfony2

Pour avoir une liste des commandes disponibles, on peut indiquer un début de commande, si celle-ci est inconnue, la console affiche les arguments similaires :
    $ php app/console arii:user
    
                                           
      [InvalidArgumentException]           
      Command "arii:user" is not defined.  
                                                       
      Did you mean one of these?           
          arii:user:create                 
          fos:user:promote                 
          fos:user:activate                
          fos:user:change-password         
          fos:user:demote                  
          fos:user:create                  
          fos:user:deactivate              
                                       
### Ajouter un utilisateur

La commande arii:user:create permet d'ajouter un utilisateur en mode interactif ou en mode batch.

Pour le mode interactif, on indique la commande et on répond aux questions :
    $ php app/console arii:user:create
    Please choose a username:eric
    Please choose an email:eric@sos-paris.com
    Please choose a password:eric
    Please choose a firstname:eric
    Please choose a lastname:ochet
    Please choose an enterprise:SOS Paris
    Created user eric


Le mode batch permet de passer l'ensemble des arguments afin de créer l'utilisateur en une seule ligne de commande.
    $ php app/console arii:user:create username mail@sos-paris.com password prenom nom entreprise
    Created user username

### Rôle de l'utilisateur

Le rôle de l'utilisateur permet de modifier l'interface en fonction du profil. Le rôle est utilisé pour afficher ou cacher des options sur l'interface web.
Le rôle ne permet pas d'autoriser les actions sur les objets d'ordonnancement, pour réaliser cela il faut se reporter aux groupes et aux filtres du module d'administration.
Les rôles disponibles actuellement sont les suivants :
| Rôle          | Description
| ROLE_READER	| Le lecteur ne dispose d'aucun droit de création ou de modification. Il s'agit généralement d'un membre extérieur à l'équipe d'exploitation à qui on fournit une simple vue des traitements.
| ROLE_OPERATOR	| L'opérateur dispose des droits d'exécution pour le lancement ou les reprise des traitements.
| ROLE_DEVELOPER | 	Le rôle du développeur est de concevoir les traitements et les scénarios d'exécution.
| ROLE_USER	 |L'utilisateur conçoit et exécute les traitements pour lesquels il dispose des droits nécessaires.
| ROLE_ADMIN	 |L'administrateur dispose de l'ensemble des droits fonctionnels sur l'exploitation. Il peut intervenir à tout niveau sur l'ordonnancement mais ne peut modifier l'infrastructure.
| ROLE_MANAGER	 |Le manager gère l'infrastructure de l'exploitation et les groupes d'utilisateurs. Il définit les composants techniques comme les connections et les communications entre les composants.

Les rôles peuvent être hiérarchisés, par exemple un ADMIN bénéficier automatiquement des droit d'un opérateur qui est lui-même utilisateur.
Les rôles et les hiérarchies peuvent être définies suivant vos besoins en éditant le fichier app/config/security.ym :
    role_hierarchy:
        ROLE_ADMIN:       [ROLE_USER,ROLE_OPERATOR,ROLE_DEVELOPPER]
        ROLE_MANAGER:     [ROLE_USER,ROLE_OPERATOR,ROLE_DEVELOPPER]
        ROLE_OPERATOR:    [ROLE_USER]
        ROLE_DEVELOPPER:  [ROLE_USER]        

### Promouvoir un utilisateur

La commande fos:user:promote ajoute un rôle à un utilisateur :
    $ php app/console fos:user:promote eric ROLE_ADMIN
    Role "ROLE_ADMIN" has been added to user "eric".

Inversement, la commande __fos:user:demote__ permet de le rétrograder :
    $ php app/console fos:user:demote eric ROLE_ADMIN
    Role "ROLE_ADMIN" has been removed from user "eric".

Un utilisateur peut avoir plusieurs rôles, dans ce cas il faudrait le promouvoir avec autant de commandes. Il est inutile de spécifier les rôles d'une même hiérarchie, un utilisateur disposant du rôle ROLE_ADMIN a automatiquement le rôle OPERATOR et DEVELOPPER qui sont sont eux mêmes USER.

### Changer un mot de passe

La commande fos:user:change-password permet de modifier le mot de passe d'un utilisateur :
    $  php app/console fos:user:change-password eric new_password
    Changed password for user eric

Si un utilisateur souhaite changer son mot de passe, il doit utiliser le formulaire adéquat dans son espace de travail, pour plus d'informations il faut se reporter à la section Erreur : source de la référence non trouvée pErreur : source de la référence non trouvéeErreur : source de la référence non trouvée.

Mise à jour
-----------
Il existe différentes méthodes pour mettre à jour le site web en fonction des droits et des normes mise en place sur votre site.

### Par internet (GitHub)
La mise à jour est réalisée par une chaîne JobScheduler exécutée par le moteur Arii. Les processus est une chaîne /Arii/Symfony2/GitHub déclenchée par autant d'ordres qu'il existe de modules.
Chaque module peut être mis à jour à la demande.
La chaîne exécute les étapes suivantes :
- Git pull : récupération des sources sur Git Hub
- Apache stop : Arrêt du serveur web
- Purge logs : Suppression des fichiers dans app/logs
- Purge cache : Suppression des fichiers dans app/cache
- Schema update : Mise à jour du schéma de la base de données
- Apache start : Démarrage du serveur web
- Check url : Vérification du démarrage par appel de l'url

### Par dépôt d'Archive (SourceForge)

SourceForge contient l'archive complète du site web, l'archive est à décompresser et à déposer dans un répertoire de publication.

