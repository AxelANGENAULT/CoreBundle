Schema
======

<ditaa name="couches">
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

<dot name="exemple">
randkir=LR

A -> B
B -> C


</dot>

Infrastructure
--------------
L'architecture repose généralement sur des composants LAMP: Linux, Apache, MySQL/MariaDB, PHP mais il est possible changer des composants:

IIS Windows n'a jamais été testé.
Pour la base de données, PostGres et Oracle peuvent être utilisés pour la grande majorité des modules.

