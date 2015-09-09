Module Core
===========
Ce module prend en charge les fonctions communes du portail:
- Design du site
- Gestion des dates
- Rôles et permissions
- Multilinguisme


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
