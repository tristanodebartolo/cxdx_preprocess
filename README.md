# Mutualiser la gestion des preprocess des variables de theme de Drupal 10

Je vous propose dans cet article de mutualiser les preprocess des variables de theme dans un module.

## Creation du module

Commençons par créer le module qui va nous permettre de deployer notre code. À noter que, les preprocess étant implémentés dans un module, les variables déclarées dans ce module seront disponible, autant en frontend qu'en backend.

Le premier fichier de notre module est le fichier `cxdx_pretreatment.info.yml`, créons-le.

> cxdx_pretreatment.info.yml

```yaml
name: 'Cxdx pretreatment'
type: module
description: 'Preprocess for nodes, terms, medias, users or others entities.'
core_version_requirement: ^9.2 || ^10
package: 'cxdx'
```

## Création du service et de son interface

Pour nous aider à traiter la data dans les preprocess, nous allons créer un service avec quelques fonctions utilitaires.

### Service

> src/Service/CxdxPretreatmentService.php

Dans le dossier `src/Service`, ajoutons le fichier `CxdxPretreatmentService.php` et les lignes suivantes :

```php
<?php

namespace Drupal\cxdx_pretreatment\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * @file
 * Contains Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService.
 */

/**
 * Class CxdxPretreatmentService.
 *
 * @package Drupal\cxdx_pretreatment\Service
 */
class CxdxPretreatmentService implements CxdxPretreatmentServiceInterface {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $acc;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $et;

  /**
   * The state keyvalue collection.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $mh;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $crm;

  /**
   * @param \Drupal\Core\Session\AccountInterface $acc
   *    The current user
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $et
   *    The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *    The state keyvalue collection service
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $mh
   *    The module handler.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $crm
   *   The current route match.
   */
  public function __construct(
    AccountInterface $acc,
    EntityTypeManagerInterface $et,
    StateInterface $state,
    ModuleHandlerInterface $mh,
    CurrentRouteMatch $crm
  ) {
    $this->acc = $acc;
    $this->et = $et;
    $this->state = $state;
    $this->mh = $mh;
    $this->crm = $crm;
  }

  /**
   * {@inheritdoc}
   */
  function modulePathByName($module_name): ?string
  {
    return $this->mh->moduleExists($module_name) ? $this->mh->getModule($module_name)->getPath() : NULL;
  }

  /**
   * {@inheritdoc}
   */
  function moduleExist($module_name): ?bool
  {
    return (bool)$this->mh->moduleExists($module_name);
  }

  /**
   * {@inheritdoc}
   */
  function isCurrentRoute(string $route): ?bool
  {
    $route_name = $this->crm->getRouteName();
    return $route_name == $route;
  }

  /**
   * {@inheritdoc}
   */
  function routeName(): ?string
  {
    return $this->crm->getRouteName();
  }

}
```

#### Injection de services dans le service

Nous allons avoir besoin de faire différents traitements, mais également de savoir qui est l'utilisateur actuellement connecter ou encore nous allons avoir besoin de connaitre le nom de la route de la page courante, pour disposer de ces différents services à l'appel du notre, nous allons les déclarer dans le constructeur de notre classe `CxdxPretreatmentService`.

```php
  /**
   * @param \Drupal\Core\Session\AccountInterface $acc
   *    The current user
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $et
   *    The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *    The state keyvalue collection service
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $mh
   *    The module handler.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $crm
   *   The current route match.
   */
  public function __construct(
    AccountInterface $acc,
    EntityTypeManagerInterface $et,
    StateInterface $state,
    ModuleHandlerInterface $mh,
    CurrentRouteMatch $crm
  ) {
    $this->acc = $acc;
    $this->et = $et;
    $this->state = $state;
    $this->mh = $mh;
    $this->crm = $crm;
  }
```

Ainsi, en déclarant le service `ModuleHandlerInterface` dans notre service, nous disposons de toutes ses méthodes. En appellant la méthode `moduleExists`, nous pouvons savoir si un module est installé ou pas.

```php
  /**
   * {@inheritdoc}
   */
  function modulePathByName($module_name): ?string
  {
    return $this->mh->moduleExists($module_name) ? $this->mh->getModule($module_name)->getPath() : NULL;
  }
```

### Interface du Service 

Nous verrons dans les prochains articles comment utiliser ces services, mais en attendant, continuons, définissons l'interface de notre service.

> src/Service/CxdxPretreatmentServiceInterface.php

Toujours dans le dossier `src/Service`, au même niveau que notre service `CxdxPretreatmentService.php`, ajoutons le fichier `CxdxPretreatmentServiceInterface.php` avec les lignes suivantes :

```php
<?php

/**
 * @file
 * Contains Drupal\cxdx_pretreatment\Service\CxdxPretreatmentServiceInterface.
 */

namespace Drupal\cxdx_pretreatment\Service;

/**
 * Interface CxdxPretreatmentServiceInterface.
 *
 * @package Drupal\cxdx_pretreatment\Service
 */

interface CxdxPretreatmentServiceInterface {

  /**
   * modulePathByName()
   *  Function that returns the path of a module
   *
   * @param $module_name
   *  The module name to find path
   *
   * @return string|null
   */
  function modulePathByName($module_name): ?string;

  /**
   * moduleExist()
   *  Function to test the existence of a module
   *
   * @param $module_name
   *  The module name of module to check if exist
   *
   * @return bool|null
   */
  function moduleExist($module_name): ?bool;

  /**
   * isCurrentRoute()
   *  Function that checks if the route it passes is the right one
   *
   * @param string $route
   *  The route name to check if exist
   *
   * @return bool|null
   */
  function isCurrentRoute(string $route): ?bool;

  /**
   * routeName()
   *  Function that returns the current route
   *
   * @return string|null
   */
  function routeName(): ?string;
}
```

En typant nos fonctions, nous nous assurons que les paramètres fournis sont toujours formatés tel que nos fonctions les attendent. Cela nous permet ensuite de placer des écouteurs afin de concevoir des scénarios alternatifs, dans le cas où des anomalies surviennent (ou autre événement).

### Activer le Service 

Pour pouvoir utiliser notre service, nous devons le déclarer à Drupal. Pour cela nous allons créer le fichier :

> cxdx_pretreatment.services.yml

Voici les lignes que nous allons ajouter à l'intérieur :

```yaml
services:
  cxdx_pretreatment.builder:
    class: Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService
    arguments: ['@current_user', '@entity_type.manager', '@state', '@module_handler', '@current_route_match']
```

Ce fichier mérite quelques explications :

Dans Drupal, les services sont des objets qui peuvent être injectés dans d'autres objets, classes ou fonctions afin de fournir des fonctionnalités spécifiques. C'est dans cet objectif que La clé `services` est utilisé. Elle nous permet de déclarer des services que nous souhaitons utiliser dans notre application à Drupal.


Dans le contexte de notre service, la clé `cxdx_pretreatment.builder` est le nom du service que l'on souhaite créer. Il est possible de nommer les services comme on le souhaite, mais il est préférable de choisir des noms clairs et descriptifs pour faciliter leur identification. 

Lorsque ce service sera appelé, Drupal instanciera automatiquement la classe `CxdxPretreatmentService`, que nous avons déclarée dans la clé `class`, grâce à son injection de dépendance.


Pour finir, la clé `arguments` est une liste de services tiers qui seront injectés dans le constructeur de la classe de notre service. Ces services sont définis sous forme de chaînes de caractères commençant par le caractère `@`, indiquant à Drupal qu'il s'agit d'un service à injecter dans le constructeur. 

Dans notre constructeur, les cinq services qui sont injectés sont :

- `@current_user` : Le service permettant de savoir qui est connecté actuellement.
- `@entity_type.manager` : Le service "Entity type manager" permettant l'accès à tous les types d'entités définis dans Drupal.
- `@state` : Le service "State", de stocker et de récupérer des paires clé-valeur à l'échelle du système.
- `@module_handler` : Le service "Module handler", fournissant des informations sur les modules activés et leurs fichiers.
- Et le service `@current_route_match` : L'objet contenant des informations sur la route actuelle.


## Service Preprocess

> src/Preprocess

Nous allons passer maintenant au coeur de notre article, les services de `preprocess`.

Pour cela nous allons créer un nouveau dossier `Preprocess` dans le dossier `src`.

Puis nous allons créer deux fichiers supplémentaires.

- PreprocessBase.php
- PreprocessBaseInterface.php

### Service PreprocessBase

> src/Preprocess/PreprocessBase.php

Nous allons d'abord créer la classe `PreprocessBase`, à partir de laquelle nous allons étendre nos classes de preprocess et qui contiendra des fonctions communes. Nous allons déclarer cette classe `abstract`.

```php
<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Generic reusable template preprocess operations.
 */
abstract class PreprocessBase implements ContainerInjectionInterface, PreprocessBaseInterface {

  /**
   * Manage drupal configurations.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  public ConfigFactoryInterface $configFactory;

  /**
   * Storing Injected PreprocessBase dependencies in class props.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Manage drupal configurations.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): PreprocessBase {
    /* @phpstan-ignore-next-line */
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Getting specific config from the config table.
   *
   * @param string $config_name
   *  The name of the config
   * @return array
   *   an array of configurations.
   *
   * @throws \Exception
   */
  protected function getConfigs(string $config_name): array {
    $config = $this->configFactory->get($config_name);

    if (count($config->getRawData()) === 0) {
      throw new \Exception('trying to access to a no existing config: ' . $config_name);
    }
    return $config->getRawData();
  }

}
```

Pour disposer du jeu de configuration de Drupal, nous injectons dans le container `config.factory` à notre classe.

```php
/**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): PreprocessBase {
    /* @phpstan-ignore-next-line */
    return new static(
      $container->get('config.factory')
    );
  }
```

### Interface PreprocessBaseInterface

> src/Preprocess/PreprocessBaseInterface.php

La fonction `main` que nous déclarons dans l'interface de notre classe `PreprocessBase` sera la fonction que l'on appellera pour injecter nos preprocess dans les pages.

```php
<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

/**
 * Provides an interface for PreprocessBase  class.
 *
 * @ingroup plugin_api
 */
interface PreprocessBaseInterface {

  /**
   * The main method.
   *
   * This the only method that needs to be
   * executed inside the theme preprocess function,
   * and it can preprocess the template variables .
   *
   * @param array $variables
   *   The variables passed to the page template
   *   throw template_preprocess_{theme}.
   */
  public function main(array &$variables): void;

}
```

## NodePreprocess

Nous pouvons maintenant passer aux preprocess. Commençons avec le preprocess des `nodes`.

> src/Preprocess/NodePreprocess.php

Dans le dossier des preprocess créons le fichier `NodePreprocess.php` et ajoutons le code suivant.

```php
<?php
declare(strict_types=1);
namespace Drupal\cxdx_pretreatment\Preprocess;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService;

/**
 * Handle all node preprocesses.
 *
 *@see template_preprocess_node
 */
final class NodePreprocess extends PreprocessBase {

  /**
   * The alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected AliasManagerInterface $aliasManager;

  /**
   * LocalBlocStorage service
   *
   * @var \Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService
   */
  private CxdxPretreatmentService $cps;

  /**
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *    The alias manager.
   * @param \Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService $cps
   *    The CxdxPreprocessService service
   */
  public function __construct(AliasManagerInterface $alias_manager, CxdxPretreatmentService $cps) {
    $this->aliasManager = $alias_manager;
    $this->cps = $cps;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): NodePreprocess {
    return new static(
      $container->get('path_alias.manager'),
      $container->get('cxdx_pretreatment.builder')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function main(array &$variables): void {
    $node = $variables['node'];
    $nid = $node->id();
    $bundle = $node->bundle();
    // Store the nid and the bunble of the content type
    $variables['nid'] = $nid;
    $variables['bundle'] = $bundle;
  }
}
```

### Injection de service

Là encore, nous pouvons ajouter des services supplémentaires.

- \Drupal\path_alias\AliasManagerInterface
  - La classe `AliasManagerInterface` afin de travailler avec des alias
- \Drupal\cxdx_pretreatment\Service\CxdxPretreatmentService
  - Notre service `CxdxPretreatmentService` avec nos fonctions sur-mesure.

Puis, après avoir renseigné le `construct` et la fonction `create` pour l'injection des services supplémentaires, nous pouvons créer notre fonction `main()`, dans laquelle nous allons décrire notre logique à injecter dans le preprocess des `node`.

```php
/**
   * {@inheritDoc}
   */
  public function main(array &$variables): void {
    $node = $variables['node'];
    $nid = $node->id();
    $bundle = $node->bundle();
    // Store the nid and the bunble of the content type
    $variables['nid'] = $nid;
    $variables['bundle'] = $bundle;
  }
```

## Injecter le nouveau preprocess dans les `nodes`

> cxdx_pretreatment.module

Nous pouvons maintenant injecter nos nouvelles variables dans les pages des noes pour en disposer dans les templates `twig`.

Pour cela, ajoutons à notre module un nouveau fichier `cxdx_pretreatment.module` et renseignons les lignes suivantes :

```php
<?php

use Drupal\cxdx_pretreatment\Preprocess\NodePreprocess;

/**
 * Implements hook_preprocess_HOOK().
 */
function cxdx_pretreatment_preprocess_node(array &$variables): void {
  \Drupal::messenger()->addMessage('cxdx_pretreatment');
  /** @var Drupal\cxdx_pretreatment\Preprocess\NodePreprocess $nodePreprocess */
  $nodePreprocess = Drupal::classResolver(NodePreprocess::class);
  $nodePreprocess->main($variables);
}
```

Voilà, nous disposons de nouvelles variables dans les tempates twigs des `nodes`, càd, tous les template `node--*.html.twig`.

### Utiliser une variable de preprocess dans un template twig

> node--*.html.twig

```twig
Nid de l'article : {{ nid }}
Bundle de l'article : {{ bundle }}
```