Nouveau module
==============

Exemple avec un module __Git__


Génération du module 
--------------------

Exécution de la commande __generate:bundle__

    D:\www\arii>\xampp\php\php app/console generate:bundle
   
   
      Welcome to the Symfony2 bundle generator
    
    
    
    Your application code must be written in bundles. This command helps
    you generate them easily.
    
    Each bundle is hosted under a namespace (like Acme/Bundle/BlogBundle).
    The namespace should begin with a "vendor" name like your company name, your
    project name, or your client name, followed by one or more optional category
    sub-namespaces, and it should end with the bundle name itself
    (which must have Bundle as a suffix).
    
    See http://symfony.com/doc/current/cookbook/bundles/best_practices.html#index-1
    for more details on bundle naming conventions.
    
    Use / instead of \  for the namespace delimiter to avoid any problem.
    
    Bundle namespace: Arii/Bundle/GitBundle

    In your code, a bundle is often referenced by its name. It can be the
    concatenation of all namespace parts but it's really up to you to come
    up with a unique name (a good practice is to start with the vendor name).
    Based on the namespace, we suggest AriiGitBundle.
    
    Bundle name [AriiGitBundle]:
    
    The bundle can be generated anywhere. The suggested default directory uses
    the standard conventions.
    
    Target directory [D:/www/arii/src]:
    
    Determine the format to use for the generated configuration.
    
    Configuration format (yml, xml, php, or annotation): yml
    
    To help you get started faster, the command can generate some
    code snippets for you.
    
    Do you want to generate the whole directory structure [no]? yes
    
    
      Summary before generation
    
    
    You are going to generate a "Arii\Bundle\GitBundle\AriiGitBundle" bundle
    in "D:/www/arii/src/" using the "yml" format.
    
    Do you confirm generation [yes]? yes
    
    
      Bundle generation
    
    
    Generating the bundle code: OK
    Checking that the bundle is autoloaded: OK
    Confirm automatic update of your Kernel [yes]?
    Enabling the bundle inside the Kernel: OK
    Confirm automatic update of the Routing [yes]?
    Importing the bundle routing resource: OK
    
    
      You can now start using the generated code!

Intégration dans le noyau
-------------------------

Ajout du bundle dans le fichier __app/appKernel.php__

                new Arii\GitBundle\AriiGitBundle(),

Intégration dans le routage
---------------------------

Ajout dans le fichier app/config/routing.yml

    # ========================================
    # Git
    # ----------------------------------------
    arii_Git:
        resource: "@AriiGitBundle/Resources/config/routing.yml"
        prefix:   /git/{_locale}
        requirements:
            _locale: en|fr|es|de|cn|ar|ru|jp

Intégration dans Ari'i
----------------------

### Routage

	arii_git_homepage:
	    pattern:  /hello/{name}
	    defaults: { _controller: AriiGitBundle:Default:index }

### Contrôleur

    <?php
    	namespace Arii\GitBundle\Controller;
    
    	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    	
    	class DefaultController extends Controller
    	{
    	    public function indexAction($name)
    	    {
    	        return $this->render('AriiGitBundle:Default:index.html.twig', array('name' => $name));
    	    }
    	}

### Vue

    Hello {{ name }}!
