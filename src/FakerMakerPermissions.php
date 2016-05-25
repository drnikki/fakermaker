<?php
/**
 * @file
 * Contains \Drupal\fakermaker\FakerMakerPermissions.
 */

namespace Drupal\fakermaker;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\fakermaker\FakerMakerPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FakerMakerPermissions implements ContainerInjectionInterface {

  protected $fakerMakerPluginManager;

  public function __construct(FakerMakerPluginManager $fakerMakerPluginManager) {
    $this->FakerMakerPluginManager = $fakerMakerPluginManager;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.fakermaker'));
  }

  function permissions() {
    $fakermaker_plugins = $this->FakerMakerPluginManager->getDefinitions();
    foreach ($fakermaker_plugins as $plugin) {

      $permission = $plugin['permission'];
      $permissions[$permission] = array(
        'title' => t($permission),
      );
    }
    return $permissions;
  }

}
