Portail Ari'i
=============

Ari'i est un portail hébergeant des modules principalement axés sur l'exploitation informatique. 

Pour toute information sur le projet: [Solutions Open Source](http://www.sosparis.com)

Une machine virtuelle pré-configurée est disponible: [machine virtuelle](http://www.sosparis.com/download/Arii64.zip)

Demo en ligne sur [arii.org](http://www.arii.org)

Socle technique
---------------
Ari'i est écrit en PHP, il nécessite donc à minima un serveur web capable d'exécuter du PHP ainsi qu'une base base de données.

### LAMP
La machine virtuelle contient les composants suivants:
- Linux Debian
- Apache2
- MariaDB
- PHP

Le code est systématiquement testé avec ces composants sur Linux et sur Windows.

Pour la partie Windows, et dans le cadre de tests, nous préconisons XAMPP qui contient l'ensemble des composants nécessaires et qui est disponible en version portable.

Pour plus d'information: [XAMPP](https://www.apachefriends.org/fr/index.html)

### Symfony2
Ari'i utilise le framework Symfony2 qui offre non seulement une bibliothèque complète mais aussi des bonnes pratiques indispensables pour le travail en équipe.

Pour plus d'information: [Symfony2](https://symfony.com/)

Outils nécessaires
------------------
Ces outils sont à installer sur le système, ils sont disponibles aussi bien sur Unix que sur Windows.

### Git
Git est un gestionnaire de code source, il permet de récupérer simplement le code à partir d'un référentiel distant comme GitHub.

Pour plus d'information: [Git](https://git-scm.com)

### Graphviz
Graphviz permet de générer des graphiques orientés à partir d'un langage de script très simple.
On indique la localisation de l'exécutable dans la variable __graphviz_dot__ du fichier de paramètre __app/config/parameters.yml__.

Pour plus d'information: [Graphviz](http://www.graphviz.org)

### Perl 
Le perl est utilisé par certains modules et sert essentiellement à la gestion des scripts d'infrastructure. Pour Windows, Strawberry propose une version portable.
On indique la localisation de l'exécutable dans la variable __perl__ du fichier de paramètre __app/config/parameters.yml__.

Pour plus d'information:
-  [Perl](https://www.perl.org/)
-  [Strawberry](http://strawberryperl.com/)

### JobScheduler
Open Source JobScheduler est un ordonnanceur open source, Ari'i l'utilise pour gérer les scripts de maintenance et les tâches de fond.
On indique la localisation de la configuration dans la variable __osjs_config__ du fichier de paramètre __app/config/parameters.yml__.

Pour plus d'information: [JobScheduler](http://www.sos-berlin.com)

Composants Symfony
------------------
Ces composants sont à intégrer dans le portail Symfony2.

### DHTMLX Suite
DHTMLX offre une bibliothèque javascript complète pour la partie frontale. L'avantage notable, outre la grande diversité de composant, est sa modularité et la facilité d'attacher les composants les nus aux autres pour créer des applications complexes.

La bibliothèque doit être téléchargée et déposée dans le répertoire __web__ de Symfony2 avec le nom __dhtmlx_gpl__

Pour plus d'information: [DHTMLX Suite](http://dhtmlx.com/docs/products/dhtmlxSuite/)

La version pro permet de bénéficier d'un support et d'accéder à de nouvelles fonctionnalités.

### DHTMLX Scheduler
La partie Scheduler offre des fonctions d'agenda pour visualiser des données temporelles 

La bibliothèque doit être téléchargée et déposée dans le répertoire __web__ de Symfony2 avec le nom __dhtmlx_scheduler__

Une autre solution est de cloner le projet github directement dans le répertoire __web__:

    git clone https://github.com/DHTMLX/scheduler.git 

puis de renommer en dhtmlx_scheduler

    mv gantt dhtmlx_scheduler

Pour plus d'information: [DHTMLX Scheduler](http://dhtmlx.com/docs/products/dhtmlxScheduler/)

### DHTMLX Gantt
Ce module permet de travailler avec des Gantt en interactif.

La bibliothèque doit être téléchargée et déposée dans le répertoire __web__ de Symfony2 avec le nom __dhtmlx_gantt__

Une autre solution est de cloner le projet github directement dans le répertoire __web__:

    git clone https://github.com/DHTMLX/gantt.git 

puis de renommer en dhtmlx_gantt

    mv gantt dhtmlx_gantt

Pour plus d'information: [DHTMLX Gantt](http://dhtmlx.com/docs/products/dhtmlxGantt/)

### DHTMLX Connector
Pour une connexion optimale entre le client et le serveur, DHTMLX fournit une bibliothèque PHP capable de communiquer avec les composants javascript.

La bibliothèque doit être téléchargée et déposée dans le répertoire __vendor__ de Symfony2 avec le nom __dhtmlx__

Une autre solution est de cloner le projet github directement dans le répertoire __vendor__:

    git clone https://github.com/DHTMLX/connector-php.git

puis de renommer en dhtmlx

    mv connector-php dhtmlx

Pour plus d'information: [DHTMLX Gantt](http://dhtmlx.com/docs/products/dhtmlxGantt/)

### Parsedown
Parsedown est utilisé pour la partie documentation en MarkDown (format utilisé pour le présent document) et pour les rst attendus par Symfony dans la partie documenation des bundles.

Pour ajouter cette bibliothèque, il suffit de cloner le projet dans le répertoire vendor de Symfony:

    git clone https://github.com/erusev/parsedown.git
    

Modules Ari'i
-------------

### UserBundle
Le module Core s'appuie sur un module [UserBundle](https://github.com/AriiPortal/UserBundle) qui fait le lien avec le module FOSUserBundle.

### Tools
Pour bénéficier d'outils supplémentaires pour Open Source JobScheduler, il est conseillé d'ajouter le [ToolsBundle](https://github.com/AriiPortal/ToolsBundle). La particularité de ces outils est de pouvoir être utlisé sans avoir installé JobScheduler.

Configuration
-------------

Contenu de **app/config/parameters.yml**:

    arii_modules:   JOC(ROLE_USER),JID(ROLE_USER),GVZ(ROLE_USER),Input(ROLE_USER),Git(ROLE_USER),Time(ROLE_USER),Config(ROLE_ADMIN),Admin(ROLE_ADMIN)
    arii_tmp: c:\temp
    arii_pro: false
    arii_save_states: false

    workspace: c:/temp
    packages:  %workspace%/packages
    perl:      c:/xampp/perl/bin/perl

Obsolete:

    skin: skyblue

__v1.5.0__
