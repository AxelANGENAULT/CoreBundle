Ari'i
=====

> Arii est un portail qui permet de piloter des moteurs d'ordonnancement informatique.

Arii est constitué d'un container et de modules qui fonctionnent dans ce container. Chaque module est dédié à une fonction précise.
Le container est communément appelé « portail ».
Le container ou portail est le module de base, il permet de gérer l'ensemble des fonctions communes utilisées par les autres modules :
- l'authentification des utilisateurs
- la gestion des langues
- les mécanismes de session
- les accès à la base de données
- la gestion d'erreurs et les audits
- l'accès aux autres modules


Modules
-------

### CoreBundle

Ce module prend en charge les fonctions communes du portail.

### UserBundle
Le module Core s'appuie sur un module portal <a href="https://github.com/AriiPortal/UserBundle" target="_blank">UserBundle</a> qui fait le lien avec le module FOSUserBundle.

### Tools
Pour bénéficier d'outils supplémentaires pour Open Source JobScheduler, il est conseillé d'ajouter le <a href="https://github.com/AriiPortal/ToolsBundle" target="_blank">ToolsBundle</a>. La particularité de ces outils est de pouvoir être utlisé sans avoir installé JobScheduler.

Configuration
-------------

Contenu de **app/config/parameters.yml**:

    arii_modules:   JOC(ROLE_USER),JID(ROLE_USER),GVZ(ROLE_USER),Input(ROLE_USER),Git(ROLE_USER),Time(ROLE_USER),Config(ROLE_ADMIN),Admin(ROLE_ADMIN)
    arii_tmp: c:\temp
    arii_save_states: false

    workspace: c:/temp
    packages:  %workspace%/packages
    perl:      c:/xampp/perl/bin/perl

Obsolete:
    arii_pro: false
    skin: skyblue

__v1.6.0__
