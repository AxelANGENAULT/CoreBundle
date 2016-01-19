JobScheduler
============

> Ari'i et travaillent OpenSource JobScheduler en symbiose, le premier sert de frontal à l'ensemble et le second gère toute la partie "back office". Les deux outils sont proposés en GPL afin de laisser une entière liberté d'utilisiation.

Installation
------------
L'ordonnanceur dédié est nommé Arii et écoute sur le port 4444, il remplit différents rôles:
- exécution des traitements internes
- supervisor pour le déploiement des objets
- gestionnaire d'évènements pour l'ochestration

Historique
----------

Ari'i est né de la volonté d'offrir une interface web à Open Source JobScheduler qui disposait d'un excellent moteur mais dont l'interface était peu utilisable pour une production informatique.

Actuellement, JobScheduler n'est qu'un module parmi bien d'autres, pour la partie planification nous avons ajouté RunDeck et Autoys afin de laisser le choix de l'outil tout en fournissant une interface standard. Certaines modules comme le "Report" peuvent gérer différents ordonnanceurs à travers une interface unique.
