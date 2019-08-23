# Tutoriel pour la création d'une API de données à partir de données existantes grâce au framework API Platform
## Notre exemple : création d’une API pour les données des organisations de data.gouv.fr reliées à Wikidata

### API Platform
[API Platform](https://api-platform.com/) est une bibliothèque puissante et facile à utiliser et pour créer des API REST pilotées par hypermédia. Elle peut être utilisée seule mais il est recommandé de l’utiliser avec le framework [Symfony](https://symfony.com/). Nous allons donc créer un projet Symfony et installer API Platform comme un bundle dans notre application web. <br/>
Nous allons créer une API pour consulter [les données des organisations de data.gouv.fr reliées à Wikidata](https://www.data.gouv.fr/fr/datasets/organisations-de-data-gouv-fr-reliees-a-wikidata/#_).

[Démo de l'API construite avec API Platform dans ce tutoriel.](http://demo-api-platform.eig-forever.org/api)


### 1. Installations prérequises
#### PHP 7.1 ou plus récent
[Documentation officielle en français](https://www.php.net/manual/fr/install.php)
#### Composer
[Composer](https://getcomposer.org/) est un outil de gestion de dépendances PHP qui nous permet d'installer les bibliothèques dont on va se servir tout au long du projet.
Pour l'installer on utilise les commandes :
```
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```
#### Client Symfony
[Symfony](https://symfony.com/) est un framework PHP pour créer des applications web.
Pour installer le client symfony, on lance la commande :
```
wget https://get.symfony.com/cli/installer -O - | bash
```
### 2. Mise en place du projet
Création du projet symfony :
```
$ composer create-project symfony/skeleton organisations_datagouv
```
Lancement du serveur :
```
$ cd organisations_datagouv
$ symfony serve -d
```
Installation de l'API :
```
$ composer require api
```
On peut consulter l'API à l'adresse https://localhost:8000/api . Pour l'instant, notre API est complètement vide et nous voulons la charger avec des données existantes que l'on récupère depuis des fichiers `.csv`.

### 3. Créer les entités en utilisant Doctrine
#### 3.1 Installation des composants et configuration de la BDD
Installation des composants nécessaires grâce à composer:
```
$ composer require migrations
$ composer require maker --dev
$ composer require profiler --dev
```
Il faut ensuite modifier le fichier `.env` qui se trouve à la racine du projet pour configurer le SGBD. Ici nous allons utiliser MySQL mais il est possible d'utiliser n'importe quel SGBD installé sur votre machine.  <br/>  Pour le configurer, il faut modifier la ligne `$DATABASE_URL` en remplaçant `db_user` et `db_pwd` par ses identifiants, et `db_name` par le nom que l'on veut donner à sa base de données.

La nouvelle valeur de `DATABASE_URL` est par exemple :
`DATABASE_URL=mysql://root:@127.0.0.1:3306/organisations_datagouv` pour les valeurs `db_user="root"` et `db_pwd=""`.

#### 3.2 Création de la base de données et fichiers csv

On peut maintenant créer la base de données :
```
$ php bin/console doctrine:database:create
```
Pour pouvoir utiliser nos fichiers `.csv` comme ressources, on les récupère sur [data.gouv.fr](https://www.data.gouv.fr/fr/datasets/organisations-de-data-gouv-fr-reliees-a-wikidata/#_) et on les place dans `/public/data/ `.

Il faut ensuite générer des entités, c'est-à-dire des tables, pour la base de données. Il suffit simplement de lancer la commande suivante et suivre les instructions :
```
$ php bin/console make:entity
```

Un petit aperçu de la création d'entité en utilisant Doctrine :
![make-entity-screenshot](https://user-images.githubusercontent.com/14167172/62034704-c8296680-b1ee-11e9-89af-c2bfb4879f78.png)


J'ai créé une entité pour chaque fichier `.csv`, j'ai donc les entités `Organisation`, `SirenDatagouv` et `Twitter`. Chaque entité contient un champ pour chaque colonne du fichier `.csv`.
Quand toutes les entités sont prêtes, on peut générer la migration :
```
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

### 4. Peupler la base de données à partir de données .csv
Pour peupler la BDD à partir des .csv, j'ai créé deux scripts php [`ImportDataCommand.php`](organisations_datagouv/src/Command/ImportDataCommand.php) et [`EmptyDataBaseCommand.php`](organisations_datagouv/src/Command/EmptyDataBaseCommand.php) .<br/>

Ces scripts doivent être placés dans `/src/Command/`  pour pouvoir les éxécuter avec la commande :
```
$ php bin/console import:csv
```
Ou bien pour vider la base de données :
```
$ php bin/console database:empty
```

Remarque : il faut modifier ces scripts php dans le cas d'une autre BDD avec des entités différentes. Les parties du code à modifier sont les suivantes :
Dans `/src/Command/ImportDataCommand.php`, remplacez les entités par vos propres entités dans la fonction `execute` :
```php
$organisation = new Organisation();
$this->importFile($input, $output, 'public/data/organisations.csv', $organisation);
$siren = new SirenDatagouv();
$this->importFile($input, $output, 'public/data/siren-datagouv.csv', $siren);
$twitter = new Twitter();
$this->importFile($input, $output, 'public/data/twitter.csv', $twitter);
```
Puis, modifiez la fonction `setData` pour remplir les champs des entités :
```php
public function setData($row, $obj)
{
  if($obj instanceof Organisation){

    // Check if this object already exists in the database.
    // if not, it is added
    $organisation = $this->getContainer()->get('doctrine')->getManager()->getRepository(Organisation::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
    if(!is_object($organisation)){

      //Set fields for an Organisation entry
      $organisation = new Organisation();
      $organisation->setDatagouvid($row['datagouvid']);
      $organisation->setItem($row['item']);
      $organisation->setItemLabel($row['itemLabel']);
      return $organisation;
    }else{
      return false;
    }
  }elseif ($obj instanceof SirenDatagouv) {

    // Check if this object already exists in the database.
    // if not, it is added
    $siren = $this->getContainer()->get('doctrine')->getManager()->getRepository(SirenDatagouv::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
    if(!is_object($siren)){

      //Set fileds for a Siren entry
      $siren = new SirenDatagouv();
      $siren->setDatagouvid($row['datagouvid']);
      $siren->setSiren(intval($row['siren']));
      return $siren;

    }else{
      return false;
    }
  }elseif ($obj instanceof Twitter) {
    // Check if this object already exists in the database.
    // if not, it is added
    $twitter = $this->getContainer()->get('doctrine')->getManager()->getRepository(Twitter::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
    if(!is_object($twitter)){

      //Set fileds for a Twitter entry
      $twitter = new Twitter();
      $twitter->setDatagouvid($row['datagouvid']);
      $twitter->setTwitterUsername($row['Twitter_username']);
      return $twitter;

    }else{
      return false;
    }
  }
}
```
Remplacez également les entités dans `/src/Command/EmptyDataBaseCommand.php` par vos propres entités si elles sont différentes. <br/>

Et voilà ! On peut maintenant consulter nos données et effectuer des requêtes en [localhost](https://localhost:8000/api) en testant les opérations par défaut.


### 5. Configurer notre API
Nous avons maintenant une API par défaut qui contient 5 opérations pour chaque ressource :
<ul>
<li> GET: Pour récupérer la liste des ressources
<li> POST: Pour créer une ressource
<li> GET: Pour récupérer une seule ressource en particulier
<li> PUT: Pour modifier une ressource
<li> DELETE: Pour supprimer une ressource
</ul>

Nous allons voir comment configurer son API pour l'adapter à ses besoins.

#### 5.1 Page d'accueil
Pour modifier le titre de l'API et sa description,  ajoutons dans le fichier `/config/packages/api_platform.yaml` les lignes suivantes :
```yaml
  title: 'Organisations de data.gouv.fr reliées à Wikidata'
  description: 'Ce jeu de données donne la liste des organisations de data.gouv.fr reliées à la base Wikidata.org. Les données sont en cours de consolidation et doivent être utilisées avec précaution.'

```
On peut retrouver la liste complète des éléments de configuration sur la [documentation d'API Platform](https://api-platform.com/docs/core/configuration/).

#### 5.2 Choix des opérations
La plupart du temps, on ne veut pas donner à n'importe qui la possibilité de modifier, voire supprimer nos ressources. On peut donc choisir de quelles opérations l'utilisateur peut disposer sur notre API. <br/>
Pour cela, on va devoir modifier nos entités PHP situées dans `/src/Entity/`. <br/>
En en-tête de la classe, on peut ajouter des paramètres supplémentaires à l'annotation `@ApiResource`
```php
* @ApiResource(
*     collectionOperations={"get"},
*     itemOperations={"get"}
* )
 ```
Les opérations de collections s'effectuent sur une liste de ressources, tandis que les opérations d'items s'effectuent sur une seule ressource en particulier. Ici, je veux seulement les opérations GET pour que l'utilisateur puisse consulter les données sans les modifier, et ce pour chacune de mes trois entités.

Voilà le résultat :

![operation-apiplatform-screenshot](https://user-images.githubusercontent.com/14167172/62034676-bc3da480-b1ee-11e9-8e12-739cbeac63e3.png)

#### 5.3 Groupes de sérialisation
Les groupes de sérialisation nous permettent de spécifier la liste des champs que l'on veut renvoyer dans les requêtes JSON. Dans notre cas, il s'agit seulement de requêtes de lecture de données mais on peut créer autant de groupe que l'on veut et les associer aux différentes opérations. <br/>
Dans notre entité `Organisation`, ajoutons un groupe de normalisation dans l'annotation `@ApiResource` :
```php
namespace App\Entity;
....
use Symfony\Component\Serializer\Annotation\Groups;
....
* @ApiResource(
 *     ....
 *     normalizationContext={"groups"={"read"}}
*      ....
 * )
 ```
Maintenant, il faut ajouter les propriétés de l'entité dans le groupe que l'on vient de créer. Pour cela, au-dessus de la propriété on ajoute une annotation `@Groups` :
```php

/**
 * L'identifiant data.gouv de l'organisation
 *
 * @ORM\Column(type="string", length=255)
 * @Groups({"read"})
 */
private $datagouvid;

```
On ajoute cette ligne pour chaque champ que l'on souhaite renvoyer lors d'une requête `GET`, et on répète l'opération sur chacune de nos entités. <br/>
En rafraichissant la page, notre API contient maintenant des modèles avec les propriétés sélectionnées. <br/>

On peut également écrire une description dans l'en-tête des attributs qui va s'afficher sur notre API.

Voilà ce que ça donne :

![models-apiplatform-screenshot](https://user-images.githubusercontent.com/14167172/62034696-c3fd4900-b1ee-11e9-84a5-115b85f4c3d9.png)


#### 5.4 Filtres de recherche
On souhaite permettre à l'utilisateur de notre API de faires des recherches sur des champs spécifiques. Pour cela, on va ajouter une nouvelle annotation à l'en-tête de notre entité `Organisation ` :
```php
namespace App\Entity;
....
use Symfony\Component\Serializer\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
....
* @ApiResource(
* ....
* )

* @ApiFilter( SearchFilter::class, properties={"datagouvid": "partial", "item":"partial", "itemLabel":"partial"})
* ....

```
Dans les properties de mon `SearchFilter`, j'ajoute les champs qui permettent d'effectuer une recherche. L'attribut `partial` indique que je souhaite que le mot recherché apparaisse dans le champ n'importe où. On peut aussi mettre `exact`, `start`, `end` ou `word_start` pour affiner la recherche. <br/>
Il existe de nombreux filtres fournis par API Platform pour faire des recherches autrement que par texte : `DateFilter` pour faire des recherches sur des champs `DateTime`, `BooleanFilter` pour des champs booléens, `RangeFilter` pour des entiers, etc. <br/>
On peut retrouver la liste de tous les filtres disponibles sur [la documentation d'API Platform](https://api-platform.com/docs/core/filters/).
On peut dorénavant tester les opérations pour récupérer des données et on obtient les requêtes au format .json.

![json-apiplatform-screenshot](https://user-images.githubusercontent.com/14167172/62034711-cbbced80-b1ee-11e9-8dbc-39cb59250167.png)

### Licence

2019 Direction interministérielle du numérique et du système
d'information et de communication de l'État. <br/>

2019 Les contributeurs accessibles via l'historique du dépôt. <br/>

Les contenus accessibles dans ce dépôt sont placés sous [Licence
Ouverte 2.0](LO.md).  Vous êtes libre de réutiliser les contenus de ce dépôt
sous les conditions précisées dans cette licence. </br>

Ce document est écrit par Gaëlle Marais à Etalab.
