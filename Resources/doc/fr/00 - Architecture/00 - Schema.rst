Schema
======

Ari'i s'appuie sur deux principaux composants logiciels:
- un backend Symfony2 renforcés par des outils PHP, Java ou Perl
- un frontal DHTMLX pour l'interface utilisateur en Javascript

Backend
-------
Le backend repose sur la norme proposée par Symfony2, les outils tiers sont généralement installés en tant que "Vendors" du portail.

<ditaa name="couches">Couches logicielles
/---------------------+---------------------+-----+---------------------\
| cGRE Module 1       | cGRE Module 2       | ... | cGRE Module n       |
+---------------------+---------------------+-----+---------------------+
| cGRE                               Core                               |
+-----------------------------------------------------------------------+
| cGRE                               User                               | 
+-----------------------------------------------------------------------+
| cGRE                             Symfony2                             |
+-----------------------------------------------------------------------+

+-----------------------------------------------------------------------+
| cBLU                               PHP                                |
+-----------------------------------------------------------------------+
| cBLU                 DB, SSH, etc...                                  |
+-----------------------------------------------------------------------+

+-----------------+--------------------------------------+--------------+
| cRED Apache     | cRED MySQL/MariaDB,PostGresOracle    | cRED SSH     | 
+-----------------+-------------+------------------------+--------------+
| cRED             Windows      | cRED               Linux              |
+-------------------------------+---------------------------------------+
</ditaa>


Infrastructure
--------------
L'architecture repose généralement sur des composants LAMP: Linux, Apache, MySQL/MariaDB, PHP mais il est possible changer des composants:

IIS Windows n'a jamais été testé.
Pour la base de données, PostGres et Oracle peuvent être utilisés pour la grande majorité des modules.
