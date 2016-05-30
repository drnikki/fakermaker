<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Routing\FakerMakerRoutes.
 */

namespace Drupal\fakermaker\Routing;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

class FakerMakerRoutes implements ContainerInjectionInterface {

  public function __construct(PluginManagerInterface $fakermaker_manager) {
    $this->FakerMakerManager = $fakermaker_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.fakermaker')
    );
  }

  public function routes() {
    $fakermaker_plugins = $this->FakerMakerManager->getDefinitions();
    $routes = array();
    foreach ($fakermaker_plugins as $id => $plugin) {
      $label = $plugin['label'];
      $type_url_str = str_replace('_', '-', $plugin['url']);
      $routes["fakermaker.$id"] = new Route(
        "/admin/config/development/fakermaker/$type_url_str",
        array(
          '_form' => '\Drupal\fakermaker\Form\FakerMakerCreateForm',
          '_title' => "Faker " . ucwords($label),
          '_plugin_id' => $id,
        ),
        array(
          '_permission' => $plugin['permission'],
        )
      );
    }

    return $routes;
  }

}
