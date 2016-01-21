Modules
=======

> Les modules peuvent être ajoutés ou suppimés en fonction des besoins. Un module est généralement autonome, des erreurs sur le module ou sa suppression ne gène pas le fonctionnement du portail.

__Attention!__ Les modules obligatoires sont: CoreBundle, UserBundle et AdminBundle.

Noyau
-----

Le module doit être ajouté dans le fichier app/AppKernel.php:

    use Symfony\Component\HttpKernel\Kernel;
    use Symfony\Component\Config\Loader\LoaderInterface; 
  
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
                new Symfony\Bundle\SecurityBundle\SecurityBundle(),
                new Symfony\Bundle\TwigBundle\TwigBundle(),
                new Symfony\Bundle\MonologBundle\MonologBundle(),
                new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
                new Symfony\Bundle\AsseticBundle\AsseticBundle(),
                new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
                new FOS\UserBundle\FOSUserBundle(), 
                new Arii\UserBundle\AriiUserBundle(),
                new Arii\CoreBundle\AriiCoreBundle(),
                new Arii\AdminBundle\AriiAdminBundle(),
                new Arii\JIDBundle\AriiJIDBundle(),
                new Arii\DSBundle\AriiDSBundle(),
                new Arii\JOEBundle\AriiJOEBundle(),
                new Arii\JOCBundle\AriiJOCBundle(),
                new Arii\MFTBundle\AriiMFTBundle(),
                new Arii\GraphvizBundle\AriiGraphvizBundle(),
                new Arii\ATSBundle\AriiATSBundle(),
                new Arii\ReportBundle\AriiReportBundle(),
                new Arii\I5Bundle\AriiI5Bundle(),
                new Arii\TimeBundle\AriiTimeBundle(),
                new Arii\GalleryBundle\AriiGalleryBundle(),
                new Arii\HubBundle\AriiHubBundle(),
                new Arii\RunBundle\AriiRunBundle(),
                new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            );

Il est possible d'activer certains modules suivant l'environnement:

         if (in_array($this->getEnvironment(), array('dev', 'test'))) {
             $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
             $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
             $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
             $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
         }
 
         return $bundles;
     }


Routage
-------

Le routage permet de retouver les informations en fonction de la route, d'une url ou d'un contrôleur. 

L'ajout des routes d'un module se fait en incluant le routage du module dans le fichier __app/config/routing.yml__

    # ========================================
    # Core Bundle
    # ----------------------------------------
    arii_core:
        resource: "@AriiCoreBundle/Resources/config/routing.yml"
        prefix:   /home/{_locale}
        requirements:
            _locale: en|fr|es|de|cn|ar|ru|jp
                
 
Paramètrage
-----------

Un module peut nécessiter des paramètres qu'on indiquera dans le fichier __app/config/parameters.yml__:

Cette partie est utilisée par Symfony2 et ne peut être modifiée qu'avec précaution:

    # This file is auto-generated during the composer install
    parameters:
        database_driver: mysqli
        database_host: 127.0.0.1
        database_port: 3306
        database_name: arii
        database_user: root
        database_password: null
        mailer_transport: smtp
        mailer_host: 127.0.0.1
        mailer_user: null
        mailer_password: null
        locale: fr
        secret: e7e1603f5100ce280eb3a33584cd625a4ce9d26b

Ces paramètres sont propres au portail, généralement lié au CoreBundle, ils permettent de définir l'environnement technique:

    arii_modules: 'Run(ROLE_USER),Hub(ROLE_USER),ATS(ROLE_USER),I5(ROLE_USER),JID(ROLE_USER),DS(ROLE_USER),JOC(ROLE_USER),MFT(ROLE_USER),GVZ(ROLE_USER),Report(ROLE_USER),Time(ROLE_USER),Gallery(ROLE_USER),JOE(ROLE_USER),Admin(ROLE_USER)'
    workspace: 'c:/xampp/apache/arii/workspace'
    packages: '%workspace%/packages'
    perl: 'c:\Perl64\bin\perl'
    java: 'C:/Program Files (x86)/Java/jre1.8.0_60'
    graphviz_dot: 'C:\Program Files (x86)\Graphviz2.38\bin\dot.exe'
    plantuml: plantuml/plantuml.jar
    ditaa: ditaa/ditaa0_9.jar
    charset: iso
    ats_doc: '/doc/{locale}/Jobs Autosys/{JOB}.job.yml'
    color_status:
        SUCCESS: '#00cccc'
        STARTING: '#00ff33'
        RUNNING: '#00cc33'
        FAILURE: '#ff0033'
        STOPPED: '#FF0000'
        ...

Si des paramètres sont liés à un module, il doivent être stocké comme suit :
    BUNDLE:
        param1 = value


Base de données
---------------

Les tables sont créées à partir des fichiers entity de chaque module, pour créer les tables il faut exécuter la commande:
    
    php app/console doctrine:schema:create --force

Si il s'agit d'une mise à jour:
    
    php app/console doctrine:schema:update --force

Si des tables ont été créés par un module et qu'on souhaite supprimer ce module, il sera nécessaire de les supprimer manuellement.

Purge
-----

Lorsque des changements de configuration sont effectués, il est impératif de vider le cache:

    php bin/console cache:clear --env=prod





