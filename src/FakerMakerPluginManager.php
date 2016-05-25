<?php
/**
 * @file
 * Contains \Drupal\fakermaker\FakerMakerPluginManager.
 */

namespace Drupal\fakermaker;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

Class FakerMakerPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/FakerMaker', $namespaces, $module_handler, NULL, 'Drupal\fakermaker\Annotation\FakerMaker');
    $this->alterInfo('fakermaker_info');
    $this->setCacheBackend($cache_backend, 'fakermaker_plugins');
  }

}
