# Gestion des utilisateurs

## Installation des dépendances

composer require symfony/flex
composer require symfony/web-server-bundle --dev
composer require symfony/maker-bundle --dev
composer require symfony/orm-pack
composer require symfony/security
composer require annotations
composer require form
composer require validator
composer require twig-bundle
composer require security-csrf
composer require symfony/swiftmailer-bundle
composer require profiler
composer require doctrine/doctrine-fixtures-bundle --dev 

**En une seule ligne**
composer require symfony/flex symfony/web-server-bundle --dev symfony/maker-bundle --dev symfony/orm-pack symfony/security 
annotations form validator twig-bundle security-csrf symfony/swiftmailer-bundle profiler doctrine/doctrine-fixtures-bundle --dev 

Démarer le serveur 
php bin/console serveur:run *:8000

configurer le fichier .env pour la connection à la base de données.

php bin/console doctrine:database:create

##Création du controlleur

php bin/console make:controller DefaultController

    _created: src/Controller/DefaultController.php_
    _created: templates/default/index.html.twig_

     _Success!_

## Création des vues

    _templates/public.html.twig_
    _templates/private.html.twig_

## Ajouter au controlleur

```php
class DefaultController extends AbstractController
{
    /**
     * @Route("/default", name="default")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/public", name="default")
     */
    public function public()
    {
        return $this->render('default/public.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/private", name="default")
     */
    public function private()
    {
        return $this->render('default/private.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
```
