Compatibilité
=============

Pour une intégration sur un Système d'Information, il peut être nécessaire nécessaire de réinstaller les composants dans le respect des normes du site.

Système d'exploitation
----------------------
Ari'i a été testé sur Windows, Linux Debian ou RedHat/Centos que ce soit en 32 ou 64bits.

Serveur Web
-----------
Il est possible d'utiliser Nginx au lieu d'Apache, il faudra se référer à la documentation Symfony2 pour l'installation du portail.

Aucun test n'a été effectué sur IIS, nous déconseillons donc ce serveur web.

Base de données
---------------
Cette base de données est un cache, elle peut être effacé à tout moment et reconstruite par les mécanismes Symfony2.

Le portail ne peut utiliser que MySql ou MariaDB car les mécanismes de synchronisation utilise les mécanismes d'auto-increment et de requêtes SQL multiples.

La base de données de l'automate d'exploitation peut utiliser MySql, la validation sur Oracle est en cours.

PHP
---
La version utilisée est une 5.4.

### Modules
    http://arii/arii/info.php
    
    arii@arii64:~/Symfony$ php app/check.php
    ********************************
    *                              *
    *  Symfony requirements check  *
    *                              *
    ********************************
    
    * Configuration file used by PHP: /etc/php5/cli/php.ini
    
    ** ATTENTION **
    *  The PHP CLI can use a different php.ini file
    *  than the one used with your web server.
    *  To be on the safe side, please also launch the requirements check
    *  from your web server using the web/config.php script.
    
    ** Mandatory requirements **
    
     OK       PHP version must be at least 5.3.3 (5.4.34-0+deb7u1 installed)
     OK       PHP version must not be 5.3.16 as Symfony won't work properly with it
     OK       Vendor libraries must be installed
     OK       app/cache/ directory must be writable
     OK       app/logs/ directory must be writable
     OK       date.timezone setting must be set
     OK       Configured default timezone "Europe/Paris" must be supported by your installation of PHP
     OK       json_encode() must be available
     OK       session_start() must be available
     OK       ctype_alpha() must be available
     OK       token_get_all() must be available
     OK       simplexml_import_dom() must be available
     OK       APC version must be at least 3.1.13 when using PHP 5.4
     OK       detect_unicode must be disabled in php.ini
     OK       PCRE extension must be available
    
    ** Optional recommendations **
    
     OK       Requirements file should be up-to-date
     OK       You should use at least PHP 5.3.4 due to PHP bug #52083 in earlier versions
     OK       When using annotations you should have at least PHP 5.3.8 due to PHP bug #55156
     OK       You should not use PHP 5.4.0 due to the PHP bug #61453
     OK       When using the logout handler from the Symfony Security Component, you should have at least PHP 5.4.11 due to PHP bug #63379 (as a workaround, you can also set invalidate_session to false in the security logout handler configuration)
     OK       You should use PHP 5.3.18+ or PHP 5.4.8+ to always get nice error messages for fatal errors in the development environment due to PHP bug #61767/#60909
     OK       PCRE extension should be at least version 8.0 (8.3 installed)
     OK       PHP-XML module should be installed
     OK       mb_strlen() should be available
     OK       iconv() should be available
     OK       utf8_decode() should be available
     OK       posix_isatty() should be available
     OK       intl extension should be available
     OK       intl extension should be correctly configured
     OK       intl ICU version should be at least 4+
     OK       a PHP accelerator should be installed
     OK       short_open_tag should be disabled in php.ini
     OK       magic_quotes_gpc should be disabled in php.ini
     OK       register_globals should be disabled in php.ini
     OK       session.auto_start should be disabled in php.ini
     OK       PDO should be installed
     OK       PDO should have some drivers installed (currently available: mysql)
