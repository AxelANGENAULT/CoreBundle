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

Contenu de **app/config/security.yml**:

    security:
        encoders:
            FOS\UserBundle\Model\UserInterface: sha512
            Symfony\Component\Security\Core\User\User: plaintext

        role_hierarchy:
            ROLE_ADMIN:       [ROLE_SYSADMIN,ROLE_OPERATOR,ROLE_DEVELOPPER]
            ROLE_MANAGER:     [ROLE_USER,ROLE_OPERATOR,ROLE_DEVELOPPER]
            ROLE_OPERATOR:    [ROLE_USER]
            ROLE_DEVELOPPER:  [ROLE_USER]       
            ROLE_SYSADMIN:    [ROLE_ALLOWED_TO_SWITCH,ROLE_DEVELOPPER]

        providers:
            chain_provider:
                chain:
                    providers: [fos_userbundle, in_memory]
            in_memory:
                memory:
                    users:
                        user:  { password: userpass, roles: [ 'ROLE_USER' ] }
                        admin: { password: adminpass, roles: [ 'ROLE_ADMIN' ] }
            fos_userbundle:
                id: fos_user.user_provider.username

        firewalls:
            rest:
                pattern:    /api/.*
                anonymous: false
                http_basic:
                    realm: "Secured Area"
                    provider: fos_userbundle

            receiver:
                pattern:    /receiver/.*
                security: false

            dev:
                pattern:  ^/(_(profiler|wdt)|css|images|js|pdf)/
                security: false

            nagios:
                pattern:  ^/(nagios)/
                security: false

            public:
                pattern:  /(public)/
                security: false

            user:
                pattern:  ^/(user)/
                security: false

            # Firewall pour le cache
            cache:
                pattern:    ^/cache
                anonymous: false
                http_basic:
                    realm: "Secured Area"
                    provider: fos_userbundle

            login:
                pattern:   ^/(login$|register|resetting|sync_state)  # Les adresses de ces pages sont login, register 
                security: false

            main:
                pattern:    ^/  # Tout est protégé
                anonymous: true

                form_login:
                    provider:    fos_userbundle  # On lit l'authentification au provider définit plus haut
                    remember_me: true            # On active la possibilité du "Se souvenir de moi" (désactivé par défaut)
                    login_path: fos_user_security_login
                    check_path: fos_user_security_check
                    default_target_path: arii_Home_index
                logout:
                    path:   fos_user_security_logout
                    target: arii_Home_index
                remember_me:
                    key:      %secret%        # On définit la clé pour le remember_me (%secret% est un parametre de parameters.ini)
                    lifetime: 31536000 # 365 days in seconds

        access_control:
            #- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }

__v1.6.0__
