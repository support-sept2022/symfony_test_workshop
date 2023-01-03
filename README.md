![](https://github.com/WildCodeSchool/workshop-symfony-testing-daily-bugle/blob/main/assets/images/daily_buggle_logo.png?raw=true)

# Daily Bugle - new features

De nouvelles features sont au sprint backlog de l'équipe dev du Daily Bugle.  
- Une page pour consulter les articles récemment publiés
- L'affichage du temps de lecture estimé d'un article
- La possibilité de laisser des commentaires sur chaque article

Malheureusement, le développeur en charge de la réalisation a été appelé en urgence sur un autre projet. Tu vas devoir terminer le travail.

Il ne manque vraiment pas grand chose. Et pour produire un code robuste, tu décides d'adopter une approche TDD.

## Démarrage

Clone ce projet.  
Pense à lancer les premières commandes habituelles après un clone, à savoir :

```bash
composer install
yarn install
yarn run dev
```
Tu peux ensuite créer ton fichier `.env.local` en renseignant tes identifiants de BDD pour MYSQL. Et si tu souhaites voir ce que donne le projet dans l'état actuel, effectue la série :

```bash
php bin/console d:d:c
php bin/console d:m:m
php bin/console d:f:l
symfony serve
```
## Mise en place de l'environnement de test
Le projet que tu viens de cloner a été initialisé avec la commande `symfony new --webapp`. Tu n'as donc rien à installer pour le moment car cette commande intègre aussi `composer require --dev symfony/test-pack`.   
Tu peux vérifier en lançant :

```bash
php bin/phpunit
```

Les tests que tu vas mettre en place vont sans doute avoir besoin d'accéder aux contenus de la base de données. Pour ne pas perturber ton environnement de dev, tu vas configurer une BDD dédiée.

Comme cela est expliqué sur la [documentation de Symfony](https://symfony.com/doc/current/testing.html#configuring-a-database-for-tests), crée un fichier `.env.test.local` à partir du fichier `.env.test` et ajoutes-y la ligne suivante en prenant soin de modifier `db_user`, `db_password` et `db_name` par les valeurs appropriées.
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
Tu peux ensuite effectuer les commandes suivantes pour terminer la configuration :
```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
```

Ta base de données de test est prête. Tu peux attaquer les features 🚀.

## Simple test d'application : WebTestCase
Garde ce lien sous le coude 😎  
   https://symfony.com/doc/current/testing.html#application-tests
   
Tous les tests que tu vas réaliser devront être rangés dans le dossier `/tests` en reproduisant la même structure que celle du dossier `/src`.  
Le premier test va concerner la classe `ArticleController` puisque tu vas tester la page des articles récents gérée par la méthode `recentArticles()`.  
Dans le dossier `/tests`, il faudrait donc créer un dossier `Controller` et y mettre une classe `ArticleControllerTest` héritant de `WebTestCase`. Tu peux réaliser cette opération grâce à la commande suivante en répondant ainsi aux questions :

```bash
php bin/console make:test

Which test type would you like?:
> WebTestCase

The name of the test class (e.g. BlogPostTest):
> Controller\ArticleControllerTest

```
### Approche TDD  
1. Écriture du test  
Ouvre le fichier `ArticleControllerTest.php`, il y a déjà un peu de code. Adapte-le afin de :
    - vérifier que la route `/recent-articles` existe ;
    - tester la présence d'un H1 sur cette page contenant le texte 'Recent articles'.  

2. Vérifier l'échec  
Lance ensuite la commande suivante afin de vérifier que ton test échoue :
```
php bin/phpunit
```
3. Solution  
Modifie le code source du projet pour passer le test avec succès.
   

## Test d'intégration :  service ReadingTime

Liens utiles :  
- https://symfony.com/doc/current/testing.html#integration-tests
- https://symfony.com/doc/current/testing.html#retrieving-services-in-the-test  

Le prochain test concerne le service ReadingTime, partiellement écrit par le précédent développeur, et sa méthode `calculate()`.  
Comme tu le sais, dans Symfony les services sont collectés par le conteneur de services.  
En lançant la commande suivante, tu vas pouvoir solliciter le système d'injection de dépendances. 

```bash
php bin/console make:test

Which test type would you like?:
> KernelTestCase

The name of the test class (e.g. BlogPostTest):
> Service\ReadingTime
```

1. Ouvre le fichier `ReadingTimeTest.php`, il y aussi déjà un peu de code. la méthode de test devrait se nommer `testCalculate()`.
2. Active le moteur symfony avec `self::bootKernel()`.
3. En t'appuyant sur la documentation, accède au service `ReadingTime::class`
4. Vérifie avec `$this->assertSame()` que la méthode `calculate()` retourne bien le temps de lecture sur le ratio 250 mots / min en arrondissant à l'entier supérieur.  
Voici un exemple de tests à effectuer, tu peux en écrire plus bien entendu :
- 250 mots => 1 min
- 500 mots => 2 min
- 650 mots => 3 min
 
> Astuce, tu peux utiliser la fonction str_repeat() pour générer des mots rapidement


Lorsque les tests sont validés avec `php bin/phpunit`, il n'y a plus qu'à terminer le branchement dans le code source. Injecte ton service à la méthode `show()` du controller `ArticleController`. Le temps de lecture peut être affiché grâce à la variable `reading_time` déjà présente dans le template `show.html.twig`.

## Test d'application complexe : soumission d'un formulaire

Lien utiles : 
- https://symfony.com/doc/current/testing.html#submitting-forms
- https://symfony.com/doc/current/testing.html#resetting-the-database-automatically-before-each-test

Retour sur la classe `ArticleControllerTest`.  

Le formulaire des pages d'article ajoute bien les commentaires en base de données. Cependant, l'affichage des commentaires, une fois postés, n'a pas été terminé.

Tu vas maintenant solliciter la base de données de l'environnement de test.  
Installe cette dépendance très pratique qui permet de réinitialiser la base de données entre chaque test afin d'écrire des scénarios automatisés.
```
composer require --dev dama/doctrine-test-bundle
```
Active l'extension en adaptant le fichier `phpunit.xml.dist` comme ceci

```
<phpunit>
    <!-- ... -->

    <extensions>
        <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
    </extensions>
</phpunit>
```
https://symfony.com/doc/current/testing.html#resetting-the-database-automatically-before-each-test

Voici la marche à suivre :
1. Crée dans la classe `ArticleControllerTest` la méthode `testCommentForm()`.
2. Tu vas avoir besoin d'accéder aux champs du formulaire. En suivant la documentation, vérifie d'abord que la route `/articles/1` existe. Puis, avec ton crawler, sélectionne le bouton de soumission du formulaire `comment_submit`. Tu peux ensuite récupérer le formulaire auquel il est associé.
4. Simule l'envoi du formulaire avec un commentaire de test.
5. La soumission du fomulaire génére une redirection, il faudra que ton test suive cette redirection afin de vérifier si la page affiche bien le commentaire envoyé.  
On t'explique ici comment procéder https://symfony.com/doc/current/testing.html#redirecting.
6. Vérifie que la liste `ul#comments` contient à présent un élément `<li>` et que son texte corresponde au commentaire envoyé. Appuie-toi sur la méthode `filter()` expliquée ici https://symfony.com/doc/current/testing/dom_crawler.html

💡 Si un message d'erreur t'indique que le service ne peut être injecté, essaie de vider le cache de Symfony avec la commande `symfony console cache:clear`.
