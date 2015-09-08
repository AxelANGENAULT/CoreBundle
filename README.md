Portail Ari'i
=============
Ari'i est un portail hébergeant des modules principalement axés sur l'exploitation informatique. 

Pré-requis
----------
Modules:
- User
- Tools (pour OSJS)

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

Obslete:
    skin: skyblue

v1.5.0
