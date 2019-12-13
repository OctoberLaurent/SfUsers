# Gestion des utilisateurs
> ### Objectifs :
> Savoir gérer les utilisateurs d'une application et sécuriser les accès.



# Création d'un nouveau projet

```bash
composer create-project symfony/skeleton my-project
cd my-project
```



# Installation des dépendances

## Liste des dépendances

- `symfony/flex`
- `symfony/web-server-bundle` permet de controler le serveur interne de PHP.
- `symfony/maker-bundle` permet de créer un controleur.
- `symfony/orm-pack` permet de créer et gérer la base de données.
- `security` 
- `annotations` permet de créer une route en annotation dans notre controleur.
- `form` permet au maker de créer les formulaires
- `validator` 
- `twig-bundle` permet de gérer les vues
- `symfony/swiftmailer-bundle` gère l'envois des emails.
- `security-csrf` permet de sécurisé les envoies de données
- `profiler` 
- `doctrine/doctrine-fixtures-bundle`


## Commandes d'installation

```bash
composer require symfony/flex
composer require symfony/web-server-bundle --dev
composer require symfony/maker-bundle --dev
composer require symfony/orm-pack
composer require security
composer require annotations
composer require form
composer require validator
composer require twig-bundle
composer require symfony/swiftmailer-bundle
composer require security-csrf
composer require profiler
composer require doctrine/doctrine-fixtures-bundle --dev
```



# Démarrer le serveur Symfony

```bash
php bin/console server:run
```

# Base de données

## Configuration

Dans le fichier `.env`, modifier la ligne :

```yaml
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

- mysql : Définit le type de SGBD.
- db_user : Définit le nom d'utilisateur.
- db_password : Définit le mon de passe.
- 127.0.0.1 : Définit l'adresse du serveur.
- 3306 : Définit le port.
- db_name : Définit le nom du schéma.


## Création

```bash
php bin/console doctrine:database:create
```



# Préparer les routes

Création de trois pages `homepage`, `public` et `private`

## Création du controller

```bash
php bin/console make:controller DefaultController
```

La commande a créée le controller `src/Controller/DefaultController.php` et la vue `templates/default/index.html.twig`


## Création des vues

- `templates/default/public.html.twig`
- `templates/default/private.html.twig`


## Création des méthodes et routes

```php
/**
 * @Route("/", name="homepage")
 */
public function index()
{
    return $this->render('default/index.html.twig', []);
}

/**
 * @Route("/public", name="public")
 */
public function public()
{
    return $this->render('default/public.html.twig', []);
}

/**
 * @Route("/private", name="private")
 */
public function private()
{
    return $this->render('default/private.html.twig', []);
}
```


## Ajouter la navigation

Dans le fichier `templates/base.html.twig`

```html
// ...
<body>
    <nav>
        <a href="{{ path('homepage') }}">Accueil</a>
        <a href="{{ path('public') }}">Page publique</a>
        <a href="{{ path('private') }}">Page privée</a>
    </nav>
    {% block body %}{% endblock %}
    // ...
```



# L'entité `user`

## Création de l'entité

```bash
php bin/console make:user
```

Valider, sans modification, les suggestions du terminal

```bash
> The name of the security user class [User]:
> Do you want to store user data in the database (via Doctrine)? [yes]:
> Enter a property name that will be the unique "display" name for the user [email]:
> Does this app need to hash/check user passwords? [yes]:
```


## Ajouter des propriétés de l'entité

```bash
php bin/console make:entity User
```

Ajouter les propriété :
- **firstname** : Prénom de l'utilisateur
    - type : string
    - length : 40
    - nullable : no
- **lastname** : Nom de l'utilisateur
    - type : string
    - length : 40
    - nullable : no
- **fullname** : Nom complet de l'utilisateur
    - type : string
    - length : 90
    - nullable : no
- **birthday** : Date de naissance
    - type : datetime
    - nullable : no
- **isActive** : Indicateur d’activation du compte
    - type : boolean
    - nullable : no
- **activateToken** : Token d'activation du compte
    - type : string
    - length : 255
    - nullable : yes
- **pwdToken** : Token de restauration du mot de passe
    - type : string
    - length : 255
    - nullable : yes


## Modifier l'entité

Dans le fichier `src/Entity/User.php`, modifier la ligne :

```php
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface {}

/**
 * @ORM\Column(type="boolean")
 */
private $isActive = false;

// ...
/**
 * @ORM\PrePersist
 */
public function setFullname(): self
{
    $this->fullname = $this->firstname;
    $this->fullname.= ' ';
    $this->fullname.= substr($this->lastname, 0, 1).".";

    return $this;
}
```


## Mise à jour de la base de données

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```


# Création des formulaires


```bash 
php bin/console make:auth
```

Valider, les suggestions du terminal

```bash
> What style of authentication do you want? [Empty authenticator]: 1 (Login form authenticator)
> The class name of the authenticator to create (e.g. AppCustomAuthenticator): LoginFormAuthenticator
> Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
> Do you want to generate a '/logout' URL? (yes/no) [yes]:
```

La commande créée ou modifie les fichiers :  

- created: `src/Security/LoginFormAuthenticator.php`
- updated: `config/packages/security.yaml`
- created: `src/Controller/SecurityController.php`
- created: `templates/security/login.html.twig`


## Ajouter un menu utilisateur

Dans le fichier `templates/base.html.twig`

```html
// ...
</nav>
<nav>
{% if is_granted('IS_AUTHENTICATED_REMEMBERED') %}
{# Authenticated user #}
    <a href="{{ path('account') }}">user name</a>
    <a href="{{ path('logout') }}">Deconnexion</a>
{% else %}
{# Anonymous user #}
    <a href="{{ path('register') }}">Inscription</a>
    <a href="{{ path('login') }}">Connexion</a>
{% endif %}
</nav>
{% block body %}{% endblock %}
// ...
```

## Ajouter les routes

Dans le fichier `src/Controller/SecurityController.php`, ajouter les routes et méthodes `register`et `account`.

```php
/**
 * @Route("/register", name="register")
 */
public function register()
{
    return $this->render('security/register.html.twig');
}

/**
 * @Route("/account", name="account")
 */
public function account()
{
    return $this->render('security/account.html.twig');
}
```

## Créer les vues

- `templates/security/register.html.twig`
- `templates/security/account.html.twig`

**`register.htlml.twig`**

```html
{% extends 'base.html.twig' %}

{% block title %}Symfony Users{% endblock %}

{% block body %}
    <h1>Register</h1>
{% endblock %}
```

**`account.htlml.twig`**

```html
{% extends 'base.html.twig' %}

{% block title %}Symfony Users{% endblock %}

{% block body %}
    <h1>Account</h1>
{% endblock %}
```
